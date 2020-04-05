<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

// Installation et mise à jour
function paniers_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		$config = lire_config('paniers');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
			'limite_ephemere' => '24',
			'limite_enregistres' => '168'
		), $config);

		if (version_compare($version_actuelle,'0.0','=')){
			// Création des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			
			ecrire_meta($nom_meta_version_base, $version_actuelle='0.0', 'non');
		}
		
		ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		ecrire_meta('paniers', serialize($config));
	}

}

// Désinstallation
function paniers_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_paniers');
	sql_drop_table('spip_paniers_liens');
		
	// On efface la version entregistrée
	effacer_meta('paniers');
	effacer_meta($nom_meta_version_base);

}

?>
