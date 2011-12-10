<?php

function stp_actualiser_paquets_locaux() {

		$paquets = stp_descriptions_paquets_locaux();
	$y = time();
		stp_base_supprimer_paquets_locaux();
		stp_base_inserer_paquets_locaux($paquets);
	$z = time();

	print_r(($z - $y) . " s<br />");
	
}

function stp_descriptions_paquets_locaux() {
	$get_infos = charger_fonction('get_infos', 'plugins');
	$plugs = $get_infos(array(), false, _DIR_PLUGINS);
	$exts  = $get_infos(array(), false, _DIR_EXTENSIONS);
	return array(
		'_DIR_PLUGINS' => $plugs,
		'_DIR_EXTENSIONS' => $exts
	);
}


// supprime les paquets et plugins locaux.
function stp_base_supprimer_paquets_locaux() {

	$id_plugins = sql_allfetsel('DISTINCT(id_plugin)', 'spip_paquets', 'local=' . sql_quote('oui'));
	$id_plugins = array_map('array_shift', $id_plugins);
	sql_delete('spip_paquets', 'local=' . sql_quote('oui'));
	$id_plugins_corrects = sql_allfetsel('DISTINCT(id_plugin)', 'spip_paquets', sql_in('id_plugin', $id_plugins));
	sql_delete('spip_plugins', sql_in('id_plugin', array_diff($id_plugins, $id_plugins_corrects)));
	
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
		'local'		 => 'oui',
	);

	$preparer_sql_paquet = charger_fonction('preparer_sql_paquet', 'plugins');

	// pour chaque decouverte, on insere les paquets en base.
	foreach($paquets_locaux as $const_dir => $paquets) {
		foreach ($paquets as $paquet) {
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
				if (!$id_plugin = sql_getfetsel('id_plugin', 'spip_plugins', 'prefixe = '.sql_quote($le_paquet['prefixe']))) {
					$id_plugin = sql_insertq('spip_plugins', $le_plugin);
				}
				
				$le_paquet['id_plugin'] = $id_plugin;
				$id_plugin = sql_insertq('spip_paquets', $le_paquet);
			}
		}
	}
}

?>
