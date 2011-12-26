<?php


function svp_actualiser_paquets_locaux() {

	spip_timer('paquets_locaux');
	$paquets = svp_descriptions_paquets_locaux();

	// un mode pour tout recalculer sans désinstaller le plugin... !
	if (_request('var_mode') == 'vider_paquets_locaux') { 
		svp_base_supprimer_paquets_locaux();
		svp_base_inserer_paquets_locaux($paquets);
	} else {
		svp_base_modifier_paquets_locaux($paquets);
	}
	svp_base_actualiser_paquets_actifs();

	$temps = spip_timer('paquets_locaux');
#spip_log('svp_actualiser_paquets_locaux', 'SVP');
#spip_log($temps, 'SVP');
	return "Éxécuté en : " . $temps;
	
}


function svp_descriptions_paquets_locaux() {
	include_spip('inc/plugin');
	liste_plugin_files(_DIR_PLUGINS);
	liste_plugin_files(_DIR_EXTENSIONS);
	$get_infos = charger_fonction('get_infos', 'plugins');
	$paquets_locaux = array(
		'_DIR_PLUGINS'    => $get_infos(array(), false, _DIR_PLUGINS),
		'_DIR_EXTENSIONS' => $get_infos(array(), false, _DIR_EXTENSIONS),
	);
	if (defined('_DIR_PLUGINS_SUPP') and _DIR_PLUGINS_SUPP) {
		liste_plugin_files(_DIR_PLUGINS_SUPP);
		$paquets_locaux['_DIR_PLUGINS_SUPP'] = $get_infos(array(), false, _DIR_PLUGINS_SUPP);
	}
	
	// creer la liste des signatures
	foreach($paquets_locaux as $const_dir => $paquets) {
		foreach ($paquets as $chemin => $paquet) {
			$paquets_locaux[$const_dir][$chemin]['signature'] = md5($const_dir . $chemin . serialize($paquet));
		}
	}
	
	return $paquets_locaux;
}


// supprime les paquets et plugins locaux.
function svp_base_supprimer_paquets_locaux() {
	sql_delete('spip_paquets', 'id_depot = ' . 0); //_paquets locaux en 0
	sql_delete('spip_plugins', sql_in('id_plugin', sql_get_select('DISTINCT(id_plugin)', 'spip_paquets'), 'NOT'));
}


/**
 * Actualise les informations en base
 * sur les paquets locaux
 * en ne modifiant que ce qui a changé.
 *
 * @param array $plugins liste d'identifiant de plugins
**/
function svp_base_modifier_paquets_locaux($paquets_locaux) {
	include_spip('inc/svp_depoter_distant');

	// On ne va modifier QUE les paquets locaux qui ont change
	// Et cela en comparant les md5 des informations fouries.
	$signatures = array();

	// recuperer toutes les signatures 
	foreach($paquets_locaux as $const_dir => $paquets) {
		foreach ($paquets as $chemin => $paquet) {
			$signatures[$paquet['signature']] = array(
				'constante' => $const_dir,
				'chemin'    => $chemin,
				'paquet'    => $paquet,
			);
		}
	}

	// tous les paquets du depot qui ne font pas parti des signatures
	$anciens_paquets = sql_allfetsel('id_paquet', 'spip_paquets', array('id_depot=' . sql_quote(0), sql_in('signature', array_keys($signatures), 'NOT')));
	$anciens_paquets = array_map('array_shift', $anciens_paquets);

	// tous les plugins correspondants aux anciens paquets
	$anciens_plugins = sql_allfetsel('p.id_plugin',	array('spip_plugins AS p', 'spip_paquets AS pa'), array('p.id_plugin=pa.id_plugin', sql_in('pa.id_paquet', $anciens_paquets)));
	$anciens_plugins = array_map('array_shift', $anciens_plugins);

	// suppression des anciens paquets
	sql_delete('spip_paquets', sql_in('id_paquet', $anciens_paquets));
	
	// corriger les vmax (et supprimer les plugins orphelins)
	svp_corriger_vmax_plugins($anciens_plugins);

	// on ne garde que les paquets qui ne sont pas presents dans la base
	$signatures_base = sql_allfetsel('signature', 'spip_paquets', 'id_depot='.sql_quote(0));
	$signatures_base = array_map('array_shift', $signatures_base);
	$signatures = array_diff_key($signatures, array_flip($signatures_base));

	// on recree la liste des paquets locaux a inserer
	$paquets_locaux = array();
	foreach ($signatures as $s => $infos) {
		if (!isset($paquets_locaux[$infos['constante']])) {
			$paquets_locaux[$infos['constante']] = array();
		}
		$paquets_locaux[$infos['constante']][$infos['chemin']] = $infos['paquet'];
	}

	svp_base_inserer_paquets_locaux($paquets_locaux);
}


/**
 * Détermine la version max
 * de chaque plugin, c'est a dire
 * la version maxi d'un des paquets qui lui est lié.
 *
 * Supprime les plugins devenus orphelins dans cette liste.
 *
 * @param array $plugins liste d'identifiant de plugins
**/
function svp_corriger_vmax_plugins($plugins) {
	// tous les plugins encore lies a des depots...
	// la vmax est a retablir...
	if ($plugins) {
		$p = sql_allfetsel('DISTINCT(p.id_plugin)', array('spip_plugins AS p', 'spip_paquets AS pa'), array(sql_in('p.id_plugin', $plugins), 'p.id_plugin=pa.id_plugin'));
		$p = array_map('array_shift', $p);
		$diff = array_diff($plugins, $p);
		// pour chaque plugin non encore utilise, on les vire !
		sql_delete('spip_plugins', sql_in('id_plugin', $diff));
	
		// pour les autres, on la fixe correctement
		$vmax = 0;
		
		// On insere, en encapsulant pour sqlite...
		if (sql_preferer_transaction()) {
			sql_demarrer_transaction();
		}
				
		foreach ($p as $id_plugin) {
			if ($pa = sql_allfetsel('version', 'spip_paquets', 'id_plugin='.$id_plugin)) {
				foreach ($pa as $v) {
					if (spip_version_compare($v['version'], $vmax, '>')) {
						$vmax = $v['version'];
					}
				}
			}
			sql_updateq('spip_plugins', array('vmax'=>$vmax), 'id_plugin=' . intval($id_plugin));
		}
		
		if (sql_preferer_transaction()) {
			sql_terminer_transaction();
		}
	}
}




function svp_base_inserer_paquets_locaux($paquets_locaux) {
	include_spip('inc/svp_depoter_distant');
	
	// On initialise les informations specifiques au paquet :
	// l'id du depot et les infos de l'archive
	$paquet_base = array(
		'id_depot' => 0,
		'nom_archive' => '',
		'nbo_archive' => '',
		'maj_archive' => '',
		'src_archive' => '',
		'date_modif' => '',
		'maj_version' => '',
		'signature' => '',
	);

	$preparer_sql_paquet = charger_fonction('preparer_sql_paquet', 'plugins');

	// pour chaque decouverte, on insere les paquets en base.
	// on evite des requetes individuelles, tres couteuses en sqlite...
	$cle_plugins    = array(); // prefixe => id
	$insert_plugins = array(); // insertion prefixe...
	$insert_paquets = array(); // insertion de paquet...

	include_spip('inc/config');
	$recents = lire_config('plugins_interessants');
	$installes  = lire_config('plugin_installes');
	$actifs  = lire_config('plugin');

	foreach($paquets_locaux as $const_dir => $paquets) {
		foreach ($paquets as $chemin => $paquet) {
			// Si on est en presence d'un plugin dont la dtd est "paquet" on compile en multi
			// les nom, slogan et description a partir des fichiers de langue.
			// De cette façon, les informations des plugins locaux et distants seront identiques
			// => On evite l'utilisation de _T() dans les squelettes
			if ($paquet['dtd'] == 'paquet') {
				$multis = svp_compiler_multis($paquet['prefix'], constant($const_dir) . '/' . $chemin);
				if (isset($multis['nom'])) $paquet['nom'] = $multis['nom'];
				if (isset($multis['slogan'])) $paquet['slogan'] = $multis['slogan'];
				if (isset($multis['description'])) $paquet['description'] = $multis['description'];
			}

			$le_paquet = $paquet_base;
			#$le_paquet['traductions'] = serialize($paquet['traductions']);

			if ($champs = $preparer_sql_paquet($paquet)) {

				// Eclater les champs recuperes en deux sous tableaux, un par table (plugin, paquet)
				$champs = eclater_plugin_paquet($champs);
				$paquet_plugin = true;
				
				// On complete les informations du paquet et du plugin
				$le_paquet = array_merge($le_paquet, $champs['paquet']);
				$le_plugin = $champs['plugin'];

				// On loge l'absence de categorie ou une categorie erronee et on positionne la categorie par defaut "aucune"
				if (!$le_plugin['categorie']) {
					$le_plugin['categorie'] = 'aucune';
				} else {
					if (!in_array($le_plugin['categorie'], $GLOBALS['categories_plugin'])) {
						$le_plugin['categorie'] = 'aucune';
					}
				}

				// creation du plugin...
				$prefixe = strtoupper( $le_plugin['prefixe'] );
				if (!isset($cle_plugins[$prefixe])) {
					if (!$id_plugin = sql_getfetsel('id_plugin', 'spip_plugins', 'prefixe = '.sql_quote($prefixe))) {
						$insert_plugins[$prefixe] = $le_plugin;
					} else {
						$cle_plugins[$prefixe] = $id_plugin;
					}
				}

				// ajout du prefixe dans le paquet, supprime avant insertion...
				$le_paquet['prefixe']     = $prefixe;
				$le_paquet['constante']   = $const_dir;
				$le_paquet['src_archive'] = $chemin;
				$le_paquet['recent']      = isset($recents[$chemin]) ? $recents[$chemin] : 0;
				$le_paquet['installe']    =  in_array($chemin, $installes) ? 'oui': 'non'; // est desinstallable ?
				$le_paquet['obsolete']    =  'non';
				$le_paquet['signature']    =  $paquet['signature'];
				
				$actif = "non";
				if (isset($actifs[$prefixe])
					and ($actifs[$prefixe]['dir_type'] == $const_dir)
					and ($actifs[$prefixe]['dir'] == $chemin)) {
					$actif = "oui";
				}
				$le_paquet['actif'] = $actif;
				// on recherche d'eventuelle mises a jour existantes
				if ($res = sql_allfetsel(
					array('pl.id_plugin', 'pa.version'),
					array('spip_plugins AS pl', 'spip_paquets AS pa'),
					array(
						'pl.id_plugin = pa.id_plugin',
						'pa.id_depot>' . sql_quote(0),
						'pl.prefixe=' . sql_quote($prefixe),
						'pa.etatnum>=' . sql_quote($le_paquet['etatnum']))))
					{

					foreach ($res as $paquet_distant) {
						// si version superieure et etat identique ou meilleur,
						// c'est que c'est une mise a jour possible !
						if (spip_version_compare($paquet_distant['version'],$le_paquet['version'],'>')) {
							if (!strlen($le_paquet['maj_version']) or spip_version_compare($paquet_distant['version'], $le_paquet['maj_version'], '>')) {
								$le_paquet['maj_version'] = $paquet_distant['version'];
							}
							# a voir si on utilisera...
							# "superieur"		=> "varchar(3) DEFAULT 'non' NOT NULL",
							# // superieur : version plus recente disponible (distant) d'un plugin (actif?) existant
						}
					}
				}
				$insert_paquets[] = $le_paquet;
			}
		}
	}

	if ($insert_plugins) {
		sql_insertq_multi('spip_plugins', $insert_plugins);
		$pls = sql_allfetsel(array('id_plugin', 'prefixe'), 'spip_plugins', sql_in('prefixe', array_keys($insert_plugins)));
		foreach ($pls as $p) {
			$cle_plugins[$p['prefixe']] = $p['id_plugin'];
		}
	}
	
	if ($insert_paquets) {

		// sert pour le calcul d'obsolescence
		$id_plugin_concernes = array();
		
		foreach ($insert_paquets as $c => $p) {
			$insert_paquets[$c]['id_plugin'] = $cle_plugins[$p['prefixe']];
			$id_plugin_concernes[ $insert_paquets[$c]['id_plugin'] ] = true;
			unset($insert_paquets[$c]['prefixe']);

			// remettre les necessite, utilise, librairie dans la cle 0
			// comme SVP
			if ($dep = unserialize($insert_paquets[$c]['dependances']) and is_array($dep)) {
				foreach ($dep as $d => $contenu) {
					if ($contenu) {
						$new = array();
						foreach($contenu as $n) {
							unset($n['id']);
							$new[ strtolower($n['nom']) ] = $n;
						}
						$dep[$d] = array($new);
					}
				}
				$insert_paquets[$c]['dependances'] = serialize($dep);
			}

		}

		sql_insertq_multi('spip_paquets', $insert_paquets);

		svp_corriger_obsolete_paquets( array_keys($id_plugin_concernes) );
	}

	if (count($cle_plugins)) {
		svp_corriger_vmax_plugins(array_values($cle_plugins));
	}
}


/**
 * Fait correspondre l'état des métas des plugins actifs & installés
 * avec ceux en base de données dans spip_paquets pour le dépot local 
**/
function svp_base_actualiser_paquets_actifs() {
	$installes  = lire_config('plugin_installes');
	$actifs  = lire_config('plugin');

	$locaux = sql_allfetsel(
		array('pa.id_paquet', 'pl.prefixe', 'pa.actif', 'pa.installe', 'pa.constante', 'pa.src_archive'),
		array('spip_paquets AS pa', 'spip_plugins AS pl'),
		array('pa.id_plugin=pl.id_plugin', 'id_depot='.sql_quote(0)));
	$changements = array();

	foreach ($locaux as $l) {
		$copie = $l;
		$prefixe = strtoupper($l['prefixe']);
		// actif ?
		if (isset($actifs[$prefixe])
			and ($actifs[$prefixe]['dir_type'] == $l['constante'])
			and ($actifs[$prefixe]['dir'] == $l['src_archive'])) {
			$copie['actif'] = "oui";
		} else {
			$copie['actif'] = "non";
		}
			
		// installe ?
		if (in_array($l['src_archive'], $installes)) {
			$copie['installe'] = "oui";
		} else {
			$copie['installe'] = "non";
		}

		if ($copie != $l) {
			$changements[ $l['id_paquet'] ] = array( 'actif'=> $copie['actif'], 'installe'=>$copie['installe'] );
		}
	}

	if (count($changements)) {
		// On insere, en encapsulant pour sqlite...
		if (sql_preferer_transaction()) {
			sql_demarrer_transaction();
		}
				
		foreach ($changements as $id_paquet => $data) {
			sql_updateq('spip_paquets', $data, 'id_paquet=' . intval($id_paquet));
		}
		
		if (sql_preferer_transaction()) {
			sql_terminer_transaction();
		}
	}

}

// Construit le contenu multi des balises nom, slogan et description a partir des items de langue
// contenus dans les fichiers paquet-prefixe_langue.php
function svp_compiler_multis($prefixe, $dir_source) {

	$multis =array();

	$module = "paquet-$prefixe";
	$item_nom = $prefixe . "_nom";
	$item_slogan = $prefixe . "_slogan";
	$item_description = $prefixe . "_description";

	// On cherche tous les fichiers de langue destines a la traduction du paquet.xml
	if ($fichiers_langue = glob($dir_source . "/lang/{$module}_*.php")) {
		$nom = $slogan = $description = '';
		foreach ($fichiers_langue as $_fichier_langue) {
			$nom_fichier = basename($_fichier_langue, '.php');
			$langue = substr($nom_fichier, strlen($module) + 1 - strlen($nom_fichier));
			// Si la langue est reconnue, on traite la liste des items de langue
			if (isset($GLOBALS['codes_langues'][$langue])) {
				$GLOBALS['idx_lang'] = $langue;
				include($_fichier_langue);
				foreach ($GLOBALS[$langue] as $_item => $_traduction) {
					if ($_item == $item_nom)
						$nom .= "\n[$langue]$_traduction";
					if ($_item == $item_slogan)
						$slogan .= "\n[$langue]$_traduction";
					if ($_item == $item_description)
						$description .= "\n[$langue]$_traduction";
				}
			}
		}

		// Finaliser la construction des balises multi
		if ($nom) $multis['nom'] = "<multi>$nom</multi>";
		if ($slogan) $multis['slogan'] = "<multi>$slogan</multi>";
		if ($description) $multis['description'] = "<multi>$description</multi>";
	}

	return $multis;
}


/**
 * Met à jour les informations d'obsolescence
 * des paquets locaux.
 *
 * @param array $ids_plugin
 * 		Identifiant de plugins concernes par les mises a jour
 * 		En cas d'absence, passera sur tous les paquets locaux
**/
function svp_corriger_obsolete_paquets($ids_plugin = array()) {
	// on minimise au maximum le nombre de requetes.
	// 1 pour lister les paquets
	// 1 pour mettre à jour les obsoletes à oui
	// 1 pour mettre à jour les obsoletes à non

	$where = array('pa.id_plugin = pl.id_plugin', 'id_depot='.sql_quote(0));
	if ($ids_plugin) {
		$where[] = sql_in('pl.id_plugin', $ids_plugin);
	}
	
	// comme l'on a de nouveaux paquets locaux...
	// certains sont peut etre devenus obsoletes
	// parmis tous les plugins locaux presents
	// concernes par les memes prefixes que les plugins ajoutes.
	$obsoletes = array();
	$changements = array();
	
	$paquets = sql_allfetsel(
		array('pa.id_paquet', 'pl.prefixe', 'pa.version', 'pa.etatnum', 'pa.obsolete'),
		array('spip_paquets AS pa', 'spip_plugins AS pl'),
		$where);

	foreach ($paquets as $c => $p) {
		
		$obsoletes[$p['prefixe']][] = $c;
		
		// si 2 paquet locaux ont le meme prefixe, mais pas la meme version,
		// l'un est obsolete : la version la plus ancienne
		if (count($obsoletes[$p['prefixe']]) > 1) {
			foreach ($obsoletes[$p['prefixe']] as $cle) {
				if ($cle == $c) continue;
				
				// je suis plus petit qu'un autre
				if (spip_version_compare($paquets[$c]['version'], $paquets[$cle]['version'], '<')) {
					if ($paquets[$c]['etatnum'] <= $paquets[$cle]['etatnum']) {
						if ($paquets[$c]['obsolete'] != 'oui') {
							$paquets[$c]['obsolete'] = 'oui';
							$changements[$c] = true;
						}
					}
				}
				
				// je suis plus grand qu'un autre...
				// si mon etat est meilleur, rendre obsolete les autres
				elseif ($paquets[$c]['etatnum'] >= $paquets[$cle]['etatnum']) {
						if ($paquets[$cle]['obsolete'] != 'oui') {
							$paquets[$cle]['obsolete'] = 'oui';
							$changements[$cle] = true;
						}
				}
				
			}
		} else {
			if ($paquets[$c]['obsolete'] != 'non') {
				$paquets[$c]['obsolete'] = 'non';
				$changements[$c] = true;
			}
		}
	}

	if (count($changements)) {
		$oui = $non = array();
		foreach ($changements as $c => $null) {
			if ($paquets[$c]['obsolete'] == 'oui') {
				$oui[] = $paquets[$c]['id_paquet'];
			} else {
				$non[] = $paquets[$c]['id_paquet'];
			}
		}

		if ($oui) {
			sql_updateq('spip_paquets', array('obsolete'=>'oui'), sql_in('id_paquet', $oui));
		}
		if ($non) {
			sql_updateq('spip_paquets', array('obsolete'=>'non'), sql_in('id_paquet', $non));
		}
	}
}
?>
