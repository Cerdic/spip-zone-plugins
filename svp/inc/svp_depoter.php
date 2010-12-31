<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/plugin');

// ----------------------- Traitements des depots ---------------------------------

/**
 * Teste la validite d'une url d'un depot de paquets
 *
 * @param string $url
 * @return boolean
 */

// $url	=> url du fichier xml de description du depot
function svp_verifier_adresse_depot($url){
	include_spip('inc/distant');
	return (!$xml = recuperer_page($url)) ? false : true;
}


/**
 * Ajout du depot et de ses extensions dans la base de donnees
 *
 * @param string $url
 * @return boolean
 */

// $url		=> url du fichier xml de description du depot
// $erreur	=> message d'erreur a afficher
function svp_ajouter_depot($url, &$erreur=''){
	// On considere que l'url a deja ete validee (correcte et nouveau depot)
	$url = trim($url);

	// Lire les donnees d'un depot de paquets
	$infos = svp_xml_parse_depot($url);
	if (!$infos) {
		$erreur = _T('svp:message_nok_xml_non_conforme', array('fichier' => $url));
		return false;
	}
	
	// Ajout du depot dans la table spip_depots. Les compteurs de paquets et de plugins
	// sont mis a jour apres le traitement des paquets
	$champs = array('titre' => filtrer_entites($infos['depot']['titre']), 
					'descriptif' => filtrer_entites($infos['depot']['descriptif']),
					'type' => $infos['depot']['type'],
					'url_serveur' => $infos['depot']['url_serveur'],
					'url_archives' => $infos['depot']['url_archives'],
					'xml_paquets'=> $url,
					'sha_paquets'=> sha1_file($url));
	$id_depot = sql_insertq('spip_depots', $champs);
		
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

	// On supprime ensuite :
	// - les liens des plugins avec le depot (table spip_depots_plugins)
	// - les plugins dont aucun paquet n'est encore heberge par un depot restant (table spip_plugins)
	// - et on met a zero la vmax des plugins ayant vu leur paquet vmax supprime
	svp_nettoyer_apres_suppression($id_depot, $vmax);

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
	$id = intval($id);
	
	// pas de depot a cet id ?
	if (!$depot = sql_fetsel('*', 'spip_depots', 'id_depot='. sql_quote($id)) ){
		return false;
	}

	$sha = sha1_file($depot['xml_paquets']);
	if ($depot['sha_paquets'] == $sha) {
		// Le fichier n'a pas change (meme sha1) alors on ne fait qu'actualiser la date 
		// de mise a jour du depot en mettant a jour *inutilement* le sha1
		spip_log('>> AVERTISSEMENT : aucune modification du fichier XML, actualisation non declenchee - id_depot = ' . $depot['id_depot'], 'svp');
		sql_replace('spip_depots', array_diff_key($depot, array('maj' => '')));
	}
	else {
		// Le fichier a bien change il faut actualiser tout le depot
		$infos = svp_xml_parse_depot($depot['xml_paquets']);
		if (!$infos)
			return false;
	
		// On actualise les paquets dans spip_paquets uniquement car le depot n'est
		// mis a jour que par le formulaire d'edition d'un depot.
		// Lors de la mise a jour des paquets, les plugins aussi sont actualises
		$ok = svp_actualiser_paquets($depot['id_depot'], $infos['paquets'], 
									$nb_paquets, $nb_plugins, $nb_autres);
		if ($ok) {
			// On met à jour le nombre de paquets et de plugins du depot ainsi que le nouveau sha1
			// ce qui aura pour effet d'actualiser la date de mise a jour
			sql_updateq('spip_depots', 
						array('nbr_paquets'=> $nb_paquets, 'nbr_plugins'=> $nb_plugins, 'nbr_autres'=> $nb_autres, 'sha_paquets'=> $sha),
						'id_depot=' . sql_quote($depot['id_depot']));
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
		
	// On initialise l'url de base des sources du depot et son type afin de calculer l'url complete de chaque logo
	$depot = sql_fetsel('url_serveur, type', 'spip_depots', 'id_depot=' . sql_quote($id_depot));
	
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

		// On verifie si le paquet est celui d'un plugin ou pas
		if ($champs = svp_remplir_champs_sql($_infos['plugin'])) {
			$paquet_plugin = true;
			// On complete les informations du paquet et du plugin
			$insert_paquet = array_merge($insert_paquet, $champs['paquet']);
			$insert_plugin = $champs['plugin'];
			// On construit l'url complete du logo
			if ($insert_paquet['logo'])
				$insert_paquet['logo'] = $depot['url_serveur'] . '/'
									   . (($depot['type'] == 'svn') ? 'export/HEAD' : '') . '/'
									   . $insert_paquet['src_archive'] . '/'
									   . $insert_paquet['logo'];

			// On loge l'absence de categorie ou une categorie erronee et on positionne la categorie
			// par defaut "aucune"
			// Provisoire tant que la DTD n'est pas en fonction
			if (!$insert_plugin['categorie']) {
				spip_log("Categorie absente dans le paquet issu de <". $insert_paquet['src_archive'] . 
						"> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets');
				$insert_plugin['categorie'] = 'aucune';
			}
			else {
				$svp_categories = unserialize($GLOBALS['meta']['svp_categories']);
				if (!in_array($insert_plugin['categorie'], $svp_categories)) {
					spip_log("Categorie &#107;" . $insert_plugin['categorie'] . "&#108; incorrecte dans le paquet issu de <". $insert_paquet['src_archive'] . 
							"> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets');
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
				// - et on exclu les themes car leur prefixe est toujours = a "theme"
				$where = array('t1.id_plugin=t2.id_plugin',
						't1.version=' . sql_quote($insert_paquet['version']),
						't1.version_base=' . sql_quote($insert_paquet['version_base']),
						't1.etatnum=' . sql_quote($insert_paquet['etatnum']),
						't2.prefixe=' . sql_quote($insert_plugin['prefixe']));
				if (($insert_plugin['prefixe'] == _SVP_PREFIXE_PLUGIN_THEME)
				OR (!$id_paquet = sql_getfetsel('t1.id_paquet', 'spip_paquets AS t1, spip_plugins AS t2', $where))) {
					// On traite d'abord le plugin du paquet pour recuperer l'id_plugin
					// On rajoute le plugin dans la table spip_plugins si celui-ci n'y est pas encore ou on recupere
					// l'id si il existe deja et on le met a jour si la version du paquet est plus elevee
					if (!$plugin = sql_fetsel('id_plugin, vmax', 'spip_plugins',
						array('prefixe=' . sql_quote($insert_plugin['prefixe'])))) {
						$id_plugin = sql_insertq('spip_plugins', 
												array_merge($insert_plugin, array('vmax' => $insert_paquet['version'])));
					}
					else {
						$id_plugin = $plugin['id_plugin'];
						if (spip_version_compare($plugin['vmax'], $insert_paquet['version'], '<='))
							sql_updateq('spip_plugins',
										array_merge($insert_plugin, array('vmax' => $insert_paquet['version'])),
										'id_plugin=' . sql_quote($id_plugin));
					}
	
					// On traite maintenant le paquet connaissant l'id du plugin
					$insert_paquet['id_plugin'] = $id_plugin;
					sql_insertq('spip_paquets', $insert_paquet);
	
					// On rajoute le plugin comme heberge par le depot si celui-ci n'est pas encore
					// enregistre comme tel
					if (!sql_countsel('spip_depots_plugins',
						array('id_plugin=' . sql_quote($id_plugin),
							'id_depot=' . sql_quote($id_depot)))) {
						sql_insertq('spip_depots_plugins', array('id_depot' => $id_depot, 'id_plugin' => $id_plugin));
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
					sql_insertq('spip_paquets', $insert_paquet);
				}
				else
					$collision = true;
			}
			// On loge le paquet ayant ete refuse dans un fichier a part afin de les verifier
			// apres coup
			if ($collision AND _SVP_LOG_PAQUETS) {
				spip_log("Collision avec le paquet <". $insert_paquet['nom_archive'] . 
						" / " . $insert_paquet['src_archive'] . "> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets');
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


// Les archives xml sont deja applaties, pas la peine de se compliquer.
function svp_remplir_champs_sql($p) {

	if (!$p)
		return array();

	// On passe le prefixe en lettres majuscules comme ce qui est fait dans SPIP
	// Ainsi les valeurs dans la table spip_plugins coincideront avec celles de la meta plugin
	$p['prefix'] = strtoupper($p['prefix']);

	// calcul du tableau de dependances
	$dependances = array();
	$v_spip = '';
	if (is_array($p['necessite'])) {
		foreach ($p['necessite'] as $c=>$n) {
			$p['necessite'][$c]['id'] = strtoupper($n['id']);
			if ($n['id'] == 'SPIP') {
				$v_spip = $n['version'];
			}
		}
		$dependances['necessite'] = $p['necessite'];
	}
	
	if (is_array($p['utilise'])) {
		foreach ($p['utilise'] as $c=>$n) {
			$p['utilise'][$c]['id'] = strtoupper($n['id']);
		}
		$dependances['utilise'] = $p['utilise'];
	}

	// Etat numerique (pour simplifier la recherche de maj)
	$num = array('stable'=>4, 'test'=>3, 'dev'=>2, 'experimental'=>1);
	$etatnum = isset($num[$p['etat']]) ? $num[$p['etat']] : 0;
	
	// On passe en utf-8 avec le bon charset les champs pouvant contenir des entites html
	$p['description'] = unicode2charset(html2unicode($p['description']));
	$p['slogan'] = unicode2charset(html2unicode($p['slogan']));
	$p['nom'] = unicode2charset(html2unicode($p['nom']));
	$p['auteur'] = unicode2charset(html2unicode($p['auteur']));
	$p['licence'] = unicode2charset(html2unicode($p['licence']));

	// Nom, slogan et branche
	if ($p['prefix'] == _SVP_PREFIXE_PLUGIN_THEME) {
		// Traitement specifique des themes qui aujourd'hui sont consideres comme des paquets
		// d'un plugin unique de prefixe "theme"
		$nom = _SVP_NOM_PLUGIN_THEME;
		$slogan = _SVP_SLOGAN_PLUGIN_THEME;
	}
	else {
		// Calcul *temporaire* de la nouvelles balise slogan si celle-ci n'est
		// pas renseignee et de la balise nom. Ceci devrait etre temporaire jusqu'a la nouvelle ere
		// glaciaire des plugins
		// - Slogan	:	si vide alors on prend la premiere phrase de la description limitee a 255
		$slogan = (!$p['slogan']) ? svp_remplir_slogan($p['description']) : $p['slogan'];
		// - Nom :	on repere dans le nom du plugin un chiffre en fin de nom
		//			et on l'ampute de ce numero pour le normaliser
		//			et on passe tout en unicode avec le charset du site
		$nom = svp_normaliser_nom($p['nom']);
	}
	
	return array(
		'plugin' => array(
			'prefixe' => $p['prefix'],
			'nom' => $nom,
			'slogan' => $slogan,
			'categorie' => $p['categorie'],
			'tags' => $p['tags']),
		'paquet' => array(
			'logo' => $p['icon'],
			'description' => $p['description'],
			'auteur' => $p['auteur'],
			'version' => $p['version'],
			'version_base' => $p['version_base'],
			'version_spip' => $v_spip,
			'etat' => $p['etat'],
			'etatnum' => $etatnum,
			'licence' => $p['licence'],
			'lien' => $p['lien'],
			'dependances' => serialize($dependances))
	);
}

function svp_remplir_slogan($description) {
	include_spip('inc/texte');

	// On extrait les traductions de l'eventuel multi
	// Si le nom n'est pas un multi alors le tableau renvoye est de la forme '' => 'nom'
	$descriptions = extraire_trads(str_replace(array('<multi>', '</multi>'), array(), $description, $nbr_replace));
	$multi = ($nbr_replace > 0) ? true : false;

	// On boucle sur chaque multi ou sur la chaine elle-meme en extrayant le slogan
	// dans les differentes langues
	$slogan = '';
	foreach ($descriptions as $_lang => $_descr) {
		$_descr = trim($_descr);
		if (!$_lang)
			$_lang = 'fr';
		$nbr_matches = preg_match(',^(.+)[.!?\r\n\f],Um', $_descr, $matches);
		$slogan .= (($multi) ? '[' . $_lang . ']' : '') . 
					(($nbr_matches > 0) ? trim($matches[1]) : couper($_descr, 150, ''));
	}

	if ($slogan)
		// On renvoie un nouveau slogan multi ou pas
		$slogan = (($multi) ? '<multi>' : '') . $slogan . (($multi) ? '</multi>' : '');

	return $slogan;
}

function svp_normaliser_nom($nom) {
	include_spip('inc/texte');

	// On extrait les traductions de l'eventuel multi
	// Si le nom n'est pas un multi alors le tableau renvoye est de la forme '' => 'nom'
	$noms = extraire_trads(str_replace(array('<multi>', '</multi>'), array(), $nom, $nbr_replace));
	$multi = ($nbr_replace > 0) ? true : false;
	
	$nouveau_nom = '';
	foreach ($noms as $_lang => $_nom) {
		$_nom = trim($_nom);
		if (!$_lang)
			$_lang = 'fr';
		$nbr_matches = preg_match(',(.+)(\s+[\d._]*)$,Um', $_nom, $matches);
		$nouveau_nom .= (($multi) ? '[' . $_lang . ']' : '') . 
						(($nbr_matches > 0) ? trim($matches[1]) : $_nom);
	}
	
	if ($nouveau_nom)
		// On renvoie un nouveau nom multi ou pas sans la valeur de la branche 
		$nouveau_nom = (($multi) ? '<multi>' : '') . $nouveau_nom . (($multi) ? '</multi>' : '');
		
	return $nouveau_nom;
}


// ----------------------- Analyses XML ---------------------------------

// parse un fichier de source dont l'url est donnee
// ce fichier est un fichier XML contenant <depot>...</depot>
// et <archives>...</archives>
function svp_xml_parse_depot($url){
	include_spip('inc/xml');
	include_spip('inc/distant');

	// On lit le fichier xml
	if (!$xml = recuperer_page($url)) {
		return false;
	}

	// On enleve la balise doctype qui provoque une erreur "balise non fermee" lors du parsage
	$xml = preg_replace('#<!DOCTYPE[^>]*>#','',$xml);

	// Deux cas d'erreur de fichier non conforme
	// - la syntaxe xml est incorrecte
	// - aucun plugin dans le depot
	// Si le bloc <depot> n'est pas renseigne on ne considere pas cela comme une erreur
	$arbre = array();
	if (!is_array($arbre = spip_xml_parse($xml)) OR !is_array($archives = $arbre['archives'][0])){
		return false;
	}

	// On extrait les informations du depot si elles existent (balise <depot>)
	$infos = array('depot' => array(), 'paquets' => array());
	if (is_array($depot = $arbre['depot'][0]))
		$infos['depot'] = svp_xml_aplatit_multiple(array('titre', 'descriptif', 'type', 'url_serveur', 'url_archives'), $depot);
	if (!$infos['depot']['titre'])
		$infos['depot']['titre'] = _T('svp:titre_nouveau_depot');
	if (!$infos['depot']['type'])
		$infos['depot']['type'] = 'svn';

	// On extrait les informations de chaque plugin du depot (balise <archives>)
	foreach ($archives as $z=>$c){
		$c = $c[0];
		// si fichier zip, on ajoute le paquet dans la liste
		// - cas 1 : c'est un plugin donc on integre les infos du plugin
		// - cas 2 : c'est une archive non plugin, pas d'infos autres que celles de l'archive
		if ($url = $c['file'][0]) {
			if (is_array($c['plugin']))
				$plugin = svp_xml_parse_plugin($c['plugin'][0]);
			else
				$plugin = array();
			// On remplit les infos dans les deux cas
			$infos['paquets'][$url] = array(
				'plugin' => $plugin, 
				'file' => $url,
				'size' => $c['size'][0],
				'date' => $c['date'][0],	// c'est la date de generation du zip
				'source' => $c['source'][0],
				'last_commit' => $c['last_commit'][0]
			);
		}
	}
	
	return $infos;
}


// aplatit plusieurs cles d'un arbre xml dans un tableau
// effectue un trim() au passage
function svp_xml_aplatit_multiple($array, $arbre){
	$a = array();
	// array('uri','archive'=>'zip',...)
	foreach ($array as $i=>$n){
		if (is_string($i)) $cle = $i;
		else $cle = $n;
		$a[$n] = trim(spip_xml_aplatit($arbre[$cle]));
	}
	return $a;	
}


// parse un plugin.xml genere par spip_xml_parse()
// en un tableau plus facilement utilisable
// cette fonction doit permettre de mapper des changements 
// de syntaxe entre plugin.xml et step
function svp_xml_parse_plugin($arbre){

	if (!is_array($arbre)) 
		return false;
	
	// on commence par les simples !
	$plug_arbre = svp_xml_aplatit_multiple(
				array('nom','icon','auteur','licence','version','version_base','etat','slogan','categorie','tags',
				'description','lien','options','fonctions','prefix','install'), 
				$arbre);
	$plug_arbre['prefix'] = strtolower($plug_arbre['prefix']);
	
	// on continue avec les plus complexes...	
	// 1) balises avec attributs
	foreach (array(
			'necessite'=>array('necessite', null),
			'utilise'=>array('utilise', null),
			'chemin'=>array('path', array('dir'=>'')))
				as $balise=>$p){
		$params = $res = array();
		// recherche de la balise et extraction des attributs
		if (spip_xml_match_nodes(",^$balise,",$arbre, $res)){
			foreach(array_keys($res) as $tag){
				list($tag,$att) = spip_xml_decompose_tag($tag);
				$params[] = $att;
			}
		} 
		// valeur par defaut
		else {
			if ($p[1]!==null)
				$params[] = $p[1];
		}
		$plug_arbre[$p[0]] = $params;		
	}

	return $plug_arbre;
}


// ----------------------- Recherches de plugins ---------------------------------

function svp_rechercher_plugins_spip($phrase, $categorie, $etat, $depot, $version_spip='',
									$exclusions=array(), $afficher_exclusions=false, $doublon=false, $tri='nom') {

	include_spip('inc/rechercher');
	
	$plugins = array();
	$scores = array();
	$ids_paquets = array();

	// On prepare l'utilisation de la recherche en base SPIP en la limitant aux tables spip_plugins
	// et spip_paquets  si elle n'est pas vide
	if ($phrase) {
		$liste = liste_des_champs();
		$tables = array('plugin' => $liste['plugin'], 'paquet' => $liste['paquet']);
		$options = array('jointures' => true, 'score' => true);
	
		// On cherche dans tous les enregistrements de ces tables des correspondances les plugins qui
		// correspondent a la phrase recherchee
		// -- On obtient une liste d'id de plugins et d'id de paquets
		$resultats = array('plugin' => array(), 'paquet' => array());
		$resultats = recherche_en_base($phrase, $tables, $options);
		// -- On prepare le tableau des scores avec les paquets trouves par la recherche
		if ($resultats) {
			// -- On convertit les id de plugins en id de paquets
			$ids = array();
			if ($resultats['plugin']) {
				$ids_plugin = array_keys($resultats['plugin']);
				$where[] = sql_in('id_plugin', $ids_plugin);
				$ids = sql_allfetsel('id_paquet, id_plugin', 'spip_paquets', $where);
			}
			// -- On prepare les listes des id de paquet et des scores de ces memes paquets
			if ($resultats['paquet']) {
				$ids_paquets = array_keys($resultats['paquet']);
				foreach ($resultats['paquet'] as $_id => $_score) {
					$scores[$_id] = intval($resultats['paquet'][$_id]['score']);
				}
			}
			// -- On merge les deux tableaux de paquets sans doublon en mettant a jour un tableau des scores
			foreach ($ids as $_ids) {
				$id_paquet = intval($_ids['id_paquet']);
				$id_plugin = intval($_ids['id_plugin']);
				if (array_search($id_paquet, $ids_paquets) === false) {
					$ids_paquets[] = $id_paquet;
					$scores[$id_paquet] = intval($resultats['plugin'][$id_plugin]['score']);
				}
				else {
					$scores[$id_paquet] = intval($resultats['paquet'][$id_paquet]['score']) 
										+ intval($resultats['plugin'][$id_plugin]['score']);
				}
			}
		}
	}

	// Maintenant, on continue la recherche en appliquant, sur la liste des id de paquets,
	// les filtres complementaires : categorie, etat, exclusions et compatibilite spip
	// si on a bien trouve des resultats precedemment ou si aucune phrase n'a ete saisie
	// -- Preparation de la requete
	if (!$phrase OR $resultats) {
		$from = array('spip_plugins AS t1', 'spip_paquets AS t2', 'spip_depots AS t3');
		$select = array('t1.nom AS nom', 't1.slogan AS slogan', 't1.prefixe AS prefixe', 't1.id_plugin AS id_plugin', 
						't2.id_paquet AS id_paquet', 't2.description AS description', 't2.version_spip AS version_spip',
						't2.auteur AS auteur', 't2.licence AS licence', 't2.etat AS etat',
						't2.logo AS logo', 't2.version AS version', 't2.nom_archive AS nom_archive',
						't3.url_archives AS url_archives', );
		$where = array('t1.id_plugin=t2.id_plugin', 't2.id_depot=t3.id_depot');
		if ($ids_paquets)
			$where[] = sql_in('t2.id_paquet', $ids_paquets);
		if (($categorie) AND ($categorie != 'toute_categorie'))
			$where[] = 't1.categorie=' . sql_quote($categorie);
		if (($etat) AND ($etat != 'tout_etat'))
			$where[] = 't2.etat=' . sql_quote($etat);
		if (($depot) AND ($depot != 'tout_depot'))
			$where[] = 't2.id_depot=' . sql_quote($depot);
		if ($exclusions AND !$afficher_exclusions)
			$where[] = sql_in('t2.id_plugin', $exclusions, 'NOT');
	
		if ($resultats = sql_select($select, $from, $where)) {
			while ($paquets = sql_fetch($resultats)) {
				$prefixe = $paquets['prefixe'];
				$version = $paquets['version'];
				$nom = extraire_multi($paquets['nom']);
				$slogan = extraire_multi($paquets['slogan']);
				$description = extraire_multi($paquets['description']);
				if (svp_verifier_compatibilite_spip($paquets['version_spip'], $version_spip)) {
					// Le paquet remplit tous les criteres, on peut le selectionner
					// -- on utilise uniquement la langue du site
					$paquets['nom'] = $nom;
					$paquets['slogan'] = $slogan;
					$paquets['description'] = $description;
					// -- on ajoute le score si on a bien saisi une phrase
					if ($phrase)
						$paquets['score'] = $scores[intval($paquets['id_paquet'])];
					else
						$paquets['score'] = 0;
					// -- on construit l'url de l'archive
					$paquets['url_archive'] = $paquets['url_archives'] . '/' . $paquets['nom_archive'];
					// -- on gere les exclusions si elle doivent etre affichees
					if ($afficher_exclusions AND in_array($paquets['id_plugin'], $exclusions))
						$paquets['installe'] = true;
					else
						$paquets['installe'] = false;
					// -- On traite les doublons (meme plugin, versions differentes)
					if ($doublon)
						// ajout systematique du paquet
						$plugins[] = $paquets;
					else {
						// ajout 
						// - si pas encore trouve 
						// - ou si sa version est inferieure (on garde que la derniere version)
						if (!$plugins[$prefixe]
						OR ($plugins[$prefixe] AND spip_version_compare($plugins[$prefixe]['version'], $version, '<'))) {
							$plugins[$prefixe] = $paquets;
						}
					}
				}
			}
		}
		
		// On trie le tableau par score décroissant ou nom croissant
		$fonction = 'svp_trier_par_' . $tri;
		if ($doublon)
			usort($plugins, $fonction);
		else
			uasort($plugins, $fonction);
	}
	
	return $plugins;
}


/**
 * Recuperation des id des plugins a exclure car deja installes
 *
 * @return array
 */
function svp_lister_plugins_installes(){

	$ids = array();

	// On recupere la liste des plugins installes physiquement sur le site
	// Pour l'instant ce n'est pas possible avec les fonctions natives de SPIP
	// donc on se contente des plugins actifs
	// - liste des prefixes en lettres majuscules des plugins actifs
	include_spip('inc/plugin');
	$plugins = liste_plugin_actifs();

	// - liste des id de plugin correspondants
	//   Il se peut que certains plugins ne soient pas trouves dans la bdd car aucun zip n'est disponible
	//   (donc pas inclus dans archives.xml). C'est le cas des extensions du core
	$ids = sql_allfetsel('id_plugin', 'spip_plugins', sql_in('prefixe', array_keys($plugins)));
	$ids = array_map('reset', $ids);
	$ids = array_map('intval', $ids);

	return $ids;
}


/**
 * Test de la compatibilite du plugin avec une version donnee de SPIP
 *
 * @return boolean
 */
function svp_verifier_compatibilite_spip($version, $version_spip) {
	include_spip('inc/plugin');
	if (!$version_spip)
		$version_spip = $GLOBALS['spip_version_branche'].".".$GLOBALS['spip_version_code'];
	return plugin_version_compatible($version, $version_spip);
}


/**
 * Tri decroissant des resultats par score. 
 * Cette fonction est appelee par un usort ou uasort
 *
 * @return int
 */
function svp_trier_par_score($p1, $p2){
	if ($p1['score'] == $p2['score']) 
		$retour = 0;
	else 
		$retour = ($p1['score'] < $p2['score']) ? 1 : -1;
	return $retour;
}


/**
 * Tri croissant des resultats par nom. 
 * Si le nom est identique on classe par version decroissante 
 * Cette fonction est appelee par un usort ou uasort
 *
 * @return int
 */
function svp_trier_par_nom($p1, $p2){
	$c1 = strcasecmp($p1['nom'], $p2['nom']);
	if ($c1 == 0) {
		$c2 = spip_version_compare($p1['version'], $p1['version'], '<');
		$retour = ($c2) ? 1 : -1;
	}
	else 
		$retour = ($c1 < 0) ? -1 : 1;
	return $retour;
}

?>
