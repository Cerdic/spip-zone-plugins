<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/plugin');
include_spip('inc/svp_phraser');

// ----------------------- Traitements des depots ---------------------------------

/**
 * Ajout du depot et de ses extensions dans la base de donnees
 *
 * @param string $url
 * @return boolean
 */

// $url		=> url du fichier xml de description du depot
// $erreur	=> message d'erreur a afficher
function svp_ajouter_depot($url, &$erreur='') {
	include_spip('inc/distant');

	// On considere que l'url a deja ete validee (correcte et nouveau depot)
	$url = trim($url);

	// Lire les donnees d'un depot de paquets
	$infos = svp_phraser_depot($url);
	if (!$infos) {
		$erreur = _T('svp:message_nok_xml_non_conforme', array('fichier' => $url));
		return false;
	}
	
	// Ajout du depot dans la table spip_depots. Les compteurs de paquets et de plugins
	// sont mis a jour apres le traitement des paquets
	$fichier_xml = _DIR_RACINE . copie_locale($url, 'force');
	$champs = array('titre' => filtrer_entites($infos['depot']['titre']), 
					'descriptif' => filtrer_entites($infos['depot']['descriptif']),
					'type' => $infos['depot']['type'],
					'url_serveur' => $infos['depot']['url_serveur'],
					'url_brouteur' => $infos['depot']['url_brouteur'],
					'url_archives' => $infos['depot']['url_archives'],
					'xml_paquets'=> $url,
					'sha_paquets'=> sha1_file($fichier_xml),
					'nbr_paquets' => 0,
					'nbr_plugins' => 0,
					'nbr_autres' => 0);
	if (!$id_depot = sql_insertq('spip_depots', $champs)) {
		$erreur = _T('svp:message_nok_sql_insert_depot', array('objet' => $titre));
		return false;
	}
		
	// Ajout des paquets dans spip_paquets et actualisation des plugins dans spip_plugins
	$ok = svp_actualiser_paquets($id_depot, $infos['paquets'], $nb_paquets, $nb_plugins, $nb_autres);
	if (!$ok OR ($nb_paquets == 0)) {
		// Si une erreur s'est produite, on supprime le depot deja insere
		sql_delete('spip_depots','id_depot='.sql_quote($id_depot));
		if (!ok)
			$erreur = _T('svp:message_nok_xml_non_conforme', array('fichier' => $url));
		else
			$erreur = _T('svp:message_nok_aucun_paquet_ajoute', array('url' => $url));
		return false;
	}

	// On met à jour le nombre de paquets et de plugins du depot maintenant !
	sql_updateq('spip_depots',
				array('nbr_paquets'=> $nb_paquets, 'nbr_plugins'=> $nb_plugins, 'nbr_autres'=> $nb_autres),
				'id_depot=' . sql_quote($id_depot));
	
	return true;
}

/**
 * Suppression du depot et de ses extensions dans la base de donnees
 *
 * @param int $id
 * @return boolean
 */

// $id	=> id_depot de l'objet depot dans la table spip_depots
function svp_supprimer_depot($id){
	$id = intval($id);
	
	// Pas de depot a cet id ?
	if (!$id_depot = sql_getfetsel('id_depot', 'spip_depots', 'id_depot='. sql_quote($id)) ){
		return false;
	}

	// on calcule les versions max des plugins heberges par le depot
	$vmax =array();
	if ($resultats = sql_select('id_plugin, version', 'spip_paquets', 'id_depot='. sql_quote($id))) {
		while ($paquet = sql_fetch($resultats)) {
			$id_plugin = $paquet['id_plugin'];
			if (!isset($vmax[$id_plugin])
			OR (spip_version_compare($vmax[$id_plugin], $paquet['version'], '<'))) 
				$vmax[$id_plugin] = $paquet['version'];
		}
	}

	// On supprime les paquets heberges par le depot
	sql_delete('spip_paquets','id_depot='.sql_quote($id_depot));
	
	// Si on est pas en mode runtime, on utilise surement l'espace public pour afficher les plugins.
	// Il faut donc verifier que les urls suivent bien la mise à jour
	// Donc avant de nettoyer la base des plugins du depot ayant disparus on supprime toutes les urls
	// associees a ce depot : on les recreera apres le nettoyage
	if (!_SVP_MODE_RUNTIME)
		svp_actualiser_url_plugins($id_depot);

	// On supprime ensuite :
	// - les liens des plugins avec le depot (table spip_depots_plugins)
	// - les plugins dont aucun paquet n'est encore heberge par un depot restant (table spip_plugins)
	// - et on met a zero la vmax des plugins ayant vu leur paquet vmax supprime
	svp_nettoyer_apres_suppression($id_depot, $vmax);

	// Si on est pas en mode runtime, on utilise surement l'espace public pour afficher les plugins.
	// Il faut donc s'assurer que les urls suivent bien la mise à jour
	// - on supprime toutes les urls plugin
	// - on les regenere pour la liste des plugins mise a jour
	if (!_SVP_MODE_RUNTIME)
		svp_actualiser_url_plugins($id_depot);

	// On supprime le depot lui-meme
	sql_delete('spip_depots','id_depot='.sql_quote($id_depot));
	return true;
}


function svp_nettoyer_apres_suppression($id_depot, $vmax) {

	// On rapatrie la liste des plugins du depot qui servira apres qu'on ait supprime les liens 
	// de la table spip_depots_plugins
	$liens = sql_allfetsel('id_plugin', 'spip_depots_plugins', 'id_depot='.sql_quote($id_depot));
	$plugins_depot = array_map('reset', $liens);

	// On peut donc supprimer tous ces liens *plugins-depots* du depot
	sql_delete('spip_depots_plugins', 'id_depot='.sql_quote($id_depot));

	// On verifie pour chaque plugin concerne par la disparition de paquets si c'est la version
	// la plus elevee qui a ete supprimee.
	// Si oui, on positionne le vmax a 0, ce qui permettra de remettre a jour le plugin systematiquement
	// a la prochaine actualisation. 
	// Cette operation est necessaire car on n'impose pas que les informations du plugin soient identiques
	// pour chaque paquet !!!
	if ($resultats = sql_select('id_plugin, vmax', 'spip_plugins', sql_in('id_plugin', $plugins_depot))) {
		while ($plugin = sql_fetch($resultats)) {
			if (spip_version_compare($plugin['vmax'], $vmax[$plugin['id_plugin']], '='))
				sql_updateq('spip_plugins',	array('vmax' => '0.0'),	'id_plugin=' . sql_quote($plugin['id_plugin']));
		}
	}

	// Maintenant on calcule la liste des plugins du depot qui ne sont pas heberges 
	// par un autre depot => donc a supprimer
	// - Liste de tous les plugins encore lies a un autre depot
	$liens = sql_allfetsel('id_plugin', 'spip_depots_plugins');
	$autres_plugins = array_map('reset', $liens);
	// - L'intersection des deux tableaux renvoie les plugins a supprimer	
	$plugins_a_supprimer = array_diff($plugins_depot, $autres_plugins);

	// On supprimer les plugins identifies
	sql_delete('spip_plugins', sql_in('id_plugin', $plugins_a_supprimer));	
	
	return true;
}


/**
 * Actualisation des plugins d'un depot deja cree.
 * @param int $id
 * @return boolean
 */

// $id	=> id_depot de l'objet depot dans la table spip_depots
function svp_actualiser_depot($id){
	include_spip('inc/distant');
	
	$id = intval($id);
	
	// pas de depot a cet id ?
	if (!$depot = sql_fetsel('*', 'spip_depots', 'id_depot='. sql_quote($id)) ){
		return false;
	}

	$fichier_xml = _DIR_RACINE . copie_locale($depot['xml_paquets'], 'force');
	$sha = sha1_file($fichier_xml);
	if ($depot['sha_paquets'] == $sha) {
		// Le fichier n'a pas change (meme sha1) alors on ne fait qu'actualiser la date 
		// de mise a jour du depot en mettant a jour *inutilement* le sha1
		spip_log('Aucune modification du fichier XML, actualisation non declenchee - id_depot = ' . $depot['id_depot'], 'svp_actions.' . _LOG_INFO);
		sql_replace('spip_depots', array_diff_key($depot, array('maj' => '')));
	}
	else {
		// Le fichier a bien change il faut actualiser tout le depot
		$infos = svp_phraser_depot($depot['xml_paquets']);
		if (!$infos)
			return false;
	
		// On actualise les paquets dans spip_paquets en premier lieu.
		// Lors de la mise a jour des paquets, les plugins aussi sont actualises
		$ok = svp_actualiser_paquets($depot['id_depot'], $infos['paquets'],
									$nb_paquets, $nb_plugins, $nb_autres);
		if ($ok) {
			// On met à jour :
			// -- les infos ne pouvant pas etre editees par le formulaire d'edition d'un depot et extraites du xml
			// -- le nombre de paquets et de plugins du depot ainsi que le nouveau sha1
			// ce qui aura pour effet d'actualiser la date de mise a jour
			$champs = array('url_serveur' => $infos['depot']['url_serveur'],
							'url_brouteur' => $infos['depot']['url_brouteur'],
							'url_archives' => $infos['depot']['url_archives'],
							'nbr_paquets'=> $nb_paquets,
							'nbr_plugins'=> $nb_plugins,
							'nbr_autres'=> $nb_autres,
							'sha_paquets'=> $sha);
			sql_updateq('spip_depots', $champs, 'id_depot=' . sql_quote($depot['id_depot']));
		}
	}
	
	return true;
}


/**
 * Actualisation de la table des paquets pour le depot choisi
 *
 * @param int $id_depot
 * @param array $paquets
 * @param int &$nb_paquets
 * @param int &$nb_plugins
 * @param int &$nb_autres
 * @return boolean
 */

// $id_depot	=> Id du depot dans la table spip_depots
// $paquets		=> Tableau des paquets extrait du fichier xml
//				   L'index est le nom de l'archive (xxxx.zip) et le contenu est
//				   un tableau à deux entrées :
//					- ['plugin'] le tableau des infos du plugin
//					- ['file'] le nom de l'archive .zip
// &$nb_paquets	=> Nombre de paquets reellement inseres dans la base renvoye a l'appelant
// &$nb_plugins	=> Nombre de plugins parmi les paquets inseres
// &$nb_autres	=> Nombre de contributions non issues de plugin parmi les paquets inseres
function svp_actualiser_paquets($id_depot, $paquets, &$nb_paquets, &$nb_plugins, &$nb_autres) {

	// Initialisation des compteurs
	$nb_paquets = 0;
	$nb_plugins = 0;
	$nb_autres = 0;
	
	// Si aucun depot ou aucun paquet on renvoie une erreur
	if ((!$id_depot) OR (!is_array($paquets)))
		return false;
		
	// On initialise l'url de base des logos du depot et son type afin de calculer l'url complete de chaque logo
	$select = array('url_brouteur', 'url_archives', 'type');
	$depot = sql_fetsel($select, 'spip_depots', 'id_depot=' . sql_quote($id_depot));
	
	// Initialisation du tableau des id de paquets crees ou mis a jour pour le depot concerne
	$ids_a_supprimer = array();
	$versions_a_supprimer = array();
	$ids = sql_allfetsel('id_paquet, id_plugin, version', 'spip_paquets', array('id_depot='. sql_quote($id_depot)));
	foreach ($ids as $_ids) {
		$ids_a_supprimer[$_ids['id_paquet']] = $_ids['id_plugin'];
		$versions_a_supprimer[$_ids['id_paquet']] = $_ids['version'];
	}

	// On met a jour ou on cree chaque paquet a partir du contenu du fichier xml
	// On ne fait pas cas de la compatibilite avec la version de SPIP installee
	// car l'operation doit permettre de collecter tous les paquets
	foreach ($paquets as $_archive => $_infos) {
		$insert_paquet = array();
		// On initialise les informations specifiques au paquet :
		// l'id du depot et les infos de l'archive
		$insert_paquet['id_depot'] = $id_depot;
		$insert_paquet['nom_archive'] = $_archive;
		$insert_paquet['nbo_archive'] = $_infos['size'];
		$insert_paquet['maj_archive'] = date('Y-m-d H:i:s', $_infos['date']);
		$insert_paquet['src_archive'] = $_infos['source'];
		$insert_paquet['date_modif'] = $_infos['last_commit'];
		// On serialise le tableau des traductions par module
		$insert_paquet['traductions'] = serialize($_infos['traductions']);

		// On verifie si le paquet est celui d'un plugin ou pas
		// -- Les traitements du XML dependent de la DTD utilisee
		// Formatage des informations extraites du plugin pour insertion dans la base SVP
		$formater = charger_fonction('preparer_sql_' . $_infos['dtd'], 'plugins');
		if ($champs_aplat = $formater($_infos['plugin'])) {
			// Eclater les champs recuperes en deux sous tableaux, un par table (plugin, paquet)
			$champs = eclater_plugin_paquet($champs_aplat);

			$paquet_plugin = true;
			// On complete les informations du paquet et du plugin
			$insert_paquet = array_merge($insert_paquet, $champs['paquet']);
			$insert_plugin = $champs['plugin'];
			// On construit l'url complete du logo
			// Le logo est maintenant disponible a la meme adresse que le zip et porte le nom du zip.
			// Son extension originale est conservee
			if ($insert_paquet['logo'])
				$insert_paquet['logo'] = $depot['url_archives'] . '/'
									   . basename($insert_paquet['nom_archive'], '.zip') . '.'
									   . pathinfo($insert_paquet['logo'], PATHINFO_EXTENSION);

			// On loge l'absence de categorie ou une categorie erronee et on positionne la categorie
			// par defaut "aucune"
			// Provisoire tant que la DTD n'est pas en fonction
			if (!$insert_plugin['categorie']) {
				spip_log("Categorie absente dans le paquet issu de <". $insert_paquet['src_archive'] . 
						"> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets.' . _LOG_INFO_IMPORTANTE);
				$insert_plugin['categorie'] = 'aucune';
			}
			else {
				$svp_categories = $GLOBALS['categories_plugin'];
				if (!in_array($insert_plugin['categorie'], $svp_categories)) {
					spip_log("Categorie &#107;" . $insert_plugin['categorie'] . "&#108; incorrecte dans le paquet issu de <". $insert_paquet['src_archive'] . 
							"> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets.' . _LOG_INFO_IMPORTANTE);
					$insert_plugin['categorie'] = 'aucune';
				}
			}
		}
		else {
			$paquet_plugin = false;
		}
		// On teste l'existence du paquet dans la base avec les champs id_depot, nom_archive et src_archive
		// pour etre sur de l'unicite.
		// - si le paquet existe on ne fait qu'un update
		// - sinon on insere le paquet
		if (!$paquet = sql_fetsel('*', 'spip_paquets', array('id_depot='. sql_quote($insert_paquet['id_depot']),
															'nom_archive='. sql_quote($insert_paquet['nom_archive']),
															'src_archive='. sql_quote($insert_paquet['src_archive'])))) {
			// Le paquet n'existe pas encore en base de donnees
			// ------------------------------------------------
			
			// On positionne la date de creation a celle du dernier commit ce qui est bien le cas
			$insert_paquet['date_crea'] = $insert_paquet['date_modif'];

			// Les collisions ne sont possibles que si on ajoute un nouveau paquet
			$collision = false;

			if ($paquet_plugin) {
				// On est en presence d'un PLUGIN
				// ------------------------------
				// On evite les doublons de paquet
				// Pour determiner un doublon on verifie actuellement :
				// - le prefixe
				// - la version du paquet et de la base
				// - l'etat
				$where = array('t1.id_plugin=t2.id_plugin',
						't1.version=' . sql_quote($insert_paquet['version']),
						't1.version_base=' . sql_quote($insert_paquet['version_base']),
						't1.etatnum=' . sql_quote($insert_paquet['etatnum']),
						't2.prefixe=' . sql_quote($insert_plugin['prefixe']));
				if (!$id_paquet = sql_getfetsel('t1.id_paquet', 'spip_paquets AS t1, spip_plugins AS t2', $where)) {
					// On traite d'abord le plugin du paquet pour recuperer l'id_plugin
					// On rajoute le plugin dans la table spip_plugins si celui-ci n'y est pas encore ou on recupere
					// l'id si il existe deja et on le met a jour si la version du paquet est plus elevee
					if (!$plugin = sql_fetsel('id_plugin, vmax', 'spip_plugins',
						array('prefixe=' . sql_quote($insert_plugin['prefixe'])))) {
						if (!$id_plugin = sql_insertq('spip_plugins', 
											array_merge($insert_plugin, array('vmax' => $insert_paquet['version']))))
							spip_log("insertion dans la base du plugin de prefixe " . $insert_plugin['prefixe'] . " archive (". $insert_paquet['nom_archive'] . ") a echoue\n", 'svp_paquets.' . _LOG_ERREUR);
					}
					else {
						$id_plugin = $plugin['id_plugin'];
						if (spip_version_compare($plugin['vmax'], $insert_paquet['version'], '<='))
							sql_updateq('spip_plugins',
										array_merge($insert_plugin, array('vmax' => $insert_paquet['version'])),
										'id_plugin=' . sql_quote($id_plugin));
					}
	
					// On traite maintenant le paquet connaissant l'id du plugin
					if ($id_plugin) {
						$insert_paquet['id_plugin'] = $id_plugin;
						if (!$id_paquet = sql_insertq('spip_paquets', $insert_paquet))
							spip_log("insertion dans la base du paquet provenant de l'archive " . $insert_paquet['nom_archive'] . " a echoue (prefixe : " . $insert_plugin['prefixe'] . ")\n", 'svp_paquets.' . _LOG_ERREUR);
						else {
							// On rajoute le plugin comme heberge par le depot si celui-ci n'est pas encore
							// enregistre comme tel
							if (!sql_countsel('spip_depots_plugins',
								array('id_plugin=' . sql_quote($id_plugin),
									'id_depot=' . sql_quote($id_depot)))) {
								sql_insertq('spip_depots_plugins', array('id_depot' => $id_depot, 'id_plugin' => $id_plugin));
							}
						}
					}
				}
				else
					$collision = true;
			}
			else {
				// On est en presence d'une CONTRIBUTION NON PLUGIN
				// ------------------------------------------------
				$where = array(
						't1.id_depot=' . sql_quote($insert_paquet['id_depot']),
						't1.nom_archive=' . sql_quote($insert_paquet['nom_archive']));
				if (!$id_paquet = sql_getfetsel('t1.id_paquet', 'spip_paquets AS t1', $where)) {
					// Ce n'est pas un plugin, donc id_plugin=0 et toutes les infos plugin sont nulles 
					$insert_paquet['id_plugin'] = 0;
					if (!$id_paquet = sql_insertq('spip_paquets', $insert_paquet))
						spip_log("insertion dans la base du paquet provenant de l'archive " . $insert_paquet['nom_archive'] . " a echoue (contribution non plugin)\n", 'svp_paquets.' . _LOG_ERREUR);
				}
				else
					$collision = true;
			}
			// On loge le paquet ayant ete refuse dans un fichier a part afin de les verifier
			// apres coup
			if ($collision) {
				spip_log("Collision avec le paquet <". $insert_paquet['nom_archive'] . 
						" / " . $insert_paquet['src_archive'] . "> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets.' . _LOG_INFO_IMPORTANTE);
			}
		}
		else {
			// Le paquet existe deja en base de donnees
			// ----------------------------------------
			
			// On met a jour le paquet en premier lieu qu'il soit un plugin ou une contribution
			sql_updateq('spip_paquets', $insert_paquet,
						'id_paquet=' . sql_quote($paquet['id_paquet']));

			// Ensuite, si on est en presence d'un plugin, on le met a jour si le paquet est de version
			// plus elevee ou egale (on gere ainsi les oublis d'incrementation)
			if ($paquet_plugin) {
				if ($vmax = sql_getfetsel('vmax', 'spip_plugins', array('id_plugin=' . sql_quote($paquet['id_plugin']))))
					if (spip_version_compare($vmax, $insert_paquet['version'], '<='))
						sql_updateq('spip_plugins',
									array_merge($insert_plugin, array('vmax' => $insert_paquet['version'])),
									'id_plugin=' . sql_quote($paquet['id_plugin']));
			}
				
			// On ne change rien sur la table spip_depots_plugins, c'est inutile

			// On retire le paquet mis a jour de la liste des paquets a supprimer a la fin de l'actualisation
			if (isset($ids_a_supprimer[$paquet['id_paquet']])) {
				unset($ids_a_supprimer[$paquet['id_paquet']]);
				unset($versions_a_supprimer[$paquet['id_paquet']]);
			}
		}
	}
	
	// Il faut maintenant nettoyer la liste des paquets et plugins qui ont disparus du depot
	if (count($ids_a_supprimer) > 0)
		svp_nettoyer_apres_actualisation($id_depot, $ids_a_supprimer, $versions_a_supprimer);

	// On compile maintenant certaines informations des paquets mis a jour dans les plugins
	// (date de creation, date de modif, version spip...)
	svp_completer_plugins($id_depot);

	// Si on est pas en mode runtime, on utilise surement l'espace public pour afficher les plugins.
	// Il faut donc s'assurer que les urls suivent bien la mise à jour
	// - on supprime toutes les urls plugin
	// - on les regenere pour la liste des plugins mise a jour
	if (!_SVP_MODE_RUNTIME)
		svp_actualiser_url_plugins($id_depot);
		
	// Calcul des compteurs de paquets, plugins et contributions
	$nb_paquets = sql_countsel('spip_paquets', 'id_depot=' . sql_quote($id_depot));
	$nb_plugins = sql_countsel('spip_depots_plugins', 'id_depot=' . sql_quote($id_depot));
	$nb_autres = sql_countsel('spip_paquets', array('id_depot=' . sql_quote($id_depot), 'id_plugin=0'));
	
	return true;
}


function svp_nettoyer_apres_actualisation($id_depot, $ids_a_supprimer, $versions_a_supprimer) {

	// Si on rentre dans cette fonction c'est que le tableau des paquets a supprimer est non vide
	// On prepare : 
	// - la liste des paquets a supprimer
	// - la liste des plugins a verifier
	// - la liste des versions max pour les plugins a verifier
	$paquets_a_supprimer = array();
	$ids_plugin = array();
	$vmax = array();
	foreach ($ids_a_supprimer as $_id_paquet => $_id_plugin) {
		$paquets_a_supprimer[] = $_id_paquet;
		if (!in_array($_id_plugin, $ids_plugin) AND ($_id_plugin != 0)) {
			$ids_plugin[] = $_id_plugin;
			if (!isset($vmax[$id_plugin])
			OR (spip_version_compare($vmax[$id_plugin], $versions_a_supprimer[$_id_paquet], '<'))) 
				$vmax[$_id_plugin] = $versions_a_supprimer[$_id_paquet];
		}
	}

	// On supprime les paquets inutiles
	sql_delete('spip_paquets', sql_in('id_paquet', $paquets_a_supprimer));

	// On verifie pour chaque plugin concerne par la disparition de paquets si c'est la version
	// la plus elevee qui a ete supprimee.
	// Si oui, on positionne le vmax a 0, ce qui permettra de remettre a jour le plugin systematiquement
	// a la prochaine actualisation. 
	// Cette operation est necessaire car on n'impose pas que les informations du plugin soient identiques
	// pour chaque paquet !!!
	if ($resultats = sql_select('id_plugin, vmax', 'spip_plugins', sql_in('id_plugin', $ids_plugin))) {
		while ($plugin = sql_fetch($resultats)) {
			if (spip_version_compare($plugin['vmax'], $vmax[$plugin['id_plugin']], '='))
				sql_updateq('spip_plugins',	array('vmax' => '0.0'),	'id_plugin=' . sql_quote($plugin['id_plugin']));
		}
	}

	if ($ids_plugin) {
		// On cherche pour chaque plugin de la liste si un paquet existe encore dans le meme depot
		// Si aucun autre paquet n'existe on peut supprimer le plugin de la table spip_depots_plugins
		if ($resultats = sql_select('id_plugin', 'spip_paquets', 
									array('id_depot=' . sql_quote($id_depot), sql_in('id_plugin', $ids_plugin)))) {
			while ($paquet = sql_fetch($resultats)) {
				$cle = array_search($paquet['id_plugin'], $ids_plugin);
				if ($cle !== false)
					unset($ids_plugin[$cle]);
			}
		}
		if (count($ids_plugin) > 0) {
			// On supprime les liens des plugins n'etant plus heberges par le depot
			sql_delete('spip_depots_plugins', array('id_depot=' . sql_quote($id_depot), sql_in('id_plugin', $ids_plugin)));
				
			// Maintenant on verifie si les plugins supprimes sont encore heberges par d'autre depot 
			// Si non, on peut supprimer le plugin lui-meme de la table spip_plugins
			$plugins_a_supprimer = $ids_plugin;
			if ($liens = sql_allfetsel('id_plugin', 'spip_depots_plugins', sql_in('id_plugin', $ids_plugin))) {
				$plugins_a_conserver = array_map('reset', $liens);
				// L'intersection des deux tableaux renvoie les plugins a supprimer	
				$plugins_a_supprimer = array_diff($ids_plugin, $plugins_a_conserver);
			}
			
			// On supprime les plugins identifies
			if ($plugins_a_supprimer)
				sql_delete('spip_plugins', sql_in('id_plugin', $plugins_a_supprimer));
		}
	}
	
	return true;
}


function svp_completer_plugins($id_depot) {
	include_spip('inc/svp_outiller');

	// On limite la revue des paquets a ceux des plugins heberges par le depot en cours d'actualisation
	if ($ids_plugin = sql_allfetsel('id_plugin', 'spip_depots_plugins', array('id_depot=' . sql_quote($id_depot)))) {
		// -- on commence donc par recuperer les id des plugins du depot
		$ids_plugin = array_map('reset', $ids_plugin);
	
		// -- on recupere tous les paquets associes aux plugins du depot et on compile les infos
		$plugin_en_cours = 0;
		if ($resultats = sql_select('id_plugin, compatibilite_spip, date_crea, date_modif', 
									'spip_paquets', 
									array(sql_in('id_plugin', $ids_plugin)), array(), 
									array('id_plugin'))) {
			while ($paquet = sql_fetch($resultats)) {
				// On finalise le plugin en cours et on passe au suivant 
				if ($plugin_en_cours != $paquet['id_plugin']) {
					// On met a jour le plugin en cours
					if ($plugin_en_cours != 0) {
						// On deduit maintenant les branches de la compatibilite globale
						$complements['branches_spip'] = compiler_branches_spip($complements['compatibilite_spip']);
						sql_updateq('spip_plugins',
									$complements,
									'id_plugin=' . sql_quote($plugin_en_cours));
					}
					// On passe au plugin suivant
					$plugin_en_cours = $paquet['id_plugin'];
					$complements = array('compatibilite_spip' => '', 'branches_spip' => '', 'date_crea' => 0, 'date_modif' => 0);
				}
				
				// On compile les compléments du plugin avec le paquet courant sauf les branches
				// qui sont deduites en fin de compilation de la compatibilite
				if ($paquet['date_modif'] > $complements['date_modif'])
					$complements['date_modif'] = $paquet['date_modif'];
				if (($complements['date_crea'] === 0)
				OR ($paquet['date_crea'] < $complements['date_crea']))
					$complements['date_crea'] = $paquet['date_crea'];
				if ($paquet['compatibilite_spip'])
					if (!$complements['compatibilite_spip'])
						$complements['compatibilite_spip'] = $paquet['compatibilite_spip'];
					else
						$complements['compatibilite_spip'] = fusionner_intervalles($paquet['compatibilite_spip'], $complements['compatibilite_spip']);
			}
			// On finalise le dernier plugin en cours
			$complements['branches_spip'] = compiler_branches_spip($complements['compatibilite_spip']);
			sql_updateq('spip_plugins',
						$complements,
						'id_plugin=' . sql_quote($plugin_en_cours));
		}
	}

	return true;
}


function svp_actualiser_url_plugins () {
	$nb_plugins = 0;

	// On supprime toutes les urls de plugin
	sql_delete('spip_urls', array('type=\'plugin\''));

	// On recupere les ids des plugins et on regenere les urls
	if ($ids_plugin = sql_allfetsel('id_plugin', 'spip_plugins')) {
		$ids_plugin = array_map('reset', $ids_plugin);
		$nb_plugins = count($ids_plugins);
		
		foreach ($ids_plugin as $_id)
			generer_url_entite($_id, 'plugin', '', '', true);
	}
	
	return $nb_plugins;
}


function eclater_plugin_paquet($champs_aplat) {
	return array(
		'plugin' => array(
			'prefixe' => $champs_aplat['prefixe'],
			'nom' => $champs_aplat['nom'],
			'slogan' => $champs_aplat['slogan'],
			'categorie' => $champs_aplat['categorie'],
			'tags' => $champs_aplat['tags']),
		'paquet' => array(
			'logo' => $champs_aplat['logo'],
			'description' => $champs_aplat['description'],
			'auteur' => $champs_aplat['auteur'],
			'credit' => $champs_aplat['credit'],
			'version' => $champs_aplat['version'],
			'version_base' => $champs_aplat['version_base'],
			'compatibilite_spip' => $champs_aplat['compatibilite_spip'],
			'branches_spip' => $champs_aplat['branches_spip'],
			'etat' => $champs_aplat['etat'],
			'etatnum' => $champs_aplat['etatnum'],
			'licence' => $champs_aplat['licence'],
			'copyright' => $champs_aplat['copyright'],
			'lien_doc' => $champs_aplat['lien_doc'],
			'lien_demo' => $champs_aplat['lien_demo'],
			'lien_dev' => $champs_aplat['lien_dev'],
			'dependances' => $champs_aplat['dependances'])
	);
}

?>
