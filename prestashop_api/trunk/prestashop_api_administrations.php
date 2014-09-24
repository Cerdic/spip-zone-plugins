<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function prestashop_api_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// $maj['create'] = array();
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function prestashop_api_vider_tables($nom_meta_base_version) {
	effacer_meta('prestashop_api');
	effacer_meta($nom_meta_base_version);
}
