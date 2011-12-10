<?php


function stp_actualiser_paquets_locaux() {

	spip_timer('paquets_locaux');
	$paquets = stp_descriptions_paquets_locaux();
	$hash = md5(serialize($paquets));
	include_spip('inc/config');
	if (lire_config('stp/hash_local') != $hash) {
		stp_base_supprimer_paquets_locaux();
		stp_base_inserer_paquets_locaux($paquets);
		ecrire_config('stp/hash_local', $hash);
	}
	$temps = spip_timer('paquets_locaux');

	return "Éxécuté en : " . $temps . "<br />";
	
}



function stp_descriptions_paquets_locaux() {
	include_spip('inc/plugin');
	liste_plugin_files(_DIR_PLUGINS);
	liste_plugin_files(_DIR_EXTENSIONS);
	$get_infos = charger_fonction('get_infos', 'plugins');
	$res = array(
		'_DIR_PLUGINS'    => $get_infos(array(), false, _DIR_PLUGINS),
		'_DIR_EXTENSIONS' => $get_infos(array(), false, _DIR_EXTENSIONS),
	);
	if (defined('_DIR_PLUGINS_SUPP') and _DIR_PLUGINS_SUPP) {
		liste_plugin_files(_DIR_PLUGINS_SUPP);
		$res['_DIR_PLUGINS_SUPP'] = $get_infos(array(), false, _DIR_PLUGINS_SUPP);
	}
	return $res;
}


// supprime les paquets et plugins locaux.
function stp_base_supprimer_paquets_locaux() {
	sql_delete('spip_paquets', 'id_depot = ' . 0); //_paquets locaux en 0
	sql_delete('spip_plugins', sql_in('id_plugin', sql_get_select('DISTINCT(id_plugin)', 'spip_paquets'), 'NOT'));
}


function stp_base_inserer_paquets_locaux($paquets_locaux) {
	include_spip('inc/svp_depoter');
	
	// On initialise les informations specifiques au paquet :
	// l'id du depot et les infos de l'archive
	$paquet_base = array(
		'id_depot' => 0,
		'nom_archive' => '',
		'nbo_archive' => '',
		'maj_archive' => '',
		'src_archive' => '',
		'date_modif' => '',
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
	var_dump($installes);
	
	foreach($paquets_locaux as $const_dir => $paquets) {
		foreach ($paquets as $chemin => $paquet) {
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
				$prefixe = $le_plugin['prefixe'];
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
				$actif = "non";
				if (isset($actifs[$prefixe])
					and ($actifs[$prefixe]['dir_type'] == $const_dir)
					and ($actifs[$prefixe]['dir'] == $chemin)) {
					$actif = "oui";
				}
				$le_paquet['actif'] = $actif;
				/*
     		"maj_version"	=> "VARCHAR(255) DEFAULT '' NOT NULL", // version superieure existante (mise a jour possible)
     		"superieur"		=> "varchar(3) DEFAULT 'non' NOT NULL", // superieur : version plus recente disponible (distant) d'un plugin (actif?) existant
     		"obsolete"		=> "varchar(3) DEFAULT 'non' NOT NULL", // obsolete : version plus ancienne (locale) disponible d'un plugin local existant
			*/
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
		foreach ($insert_paquets as $c => $p) {
			$insert_paquets[$c]['id_plugin'] = $cle_plugins[$p['prefixe']];
			unset($insert_paquets[$c]['prefixe']);
		}
		sql_insertq_multi('spip_paquets', $insert_paquets);
	}
}

?>
