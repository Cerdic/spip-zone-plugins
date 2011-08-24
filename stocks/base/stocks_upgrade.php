<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');
include_spip('base/abstract_sql');
include_spip('base/create');


// Installation et mise à jour
function stocks_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0.0';

	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$version_actuelle = $GLOBALS['meta'][$nom_meta_base_version];
	
	if (version_compare($version_actuelle,'0.0.1','<')) {
		creer_base();
		ecrire_meta($nom_meta_version_base, $version_actuelle="0.0.1");
	}

}

// Désinstallation
function stocks_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_stocks');
		
	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);

}

?>
