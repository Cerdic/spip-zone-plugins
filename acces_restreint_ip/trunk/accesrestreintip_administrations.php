<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function accesrestreintip_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('sql_alter','TABLE spip_zones ADD ips text DEFAULT "" NOT NULL')
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function accesrestreintip_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_zones DROP ips');
	effacer_meta($nom_meta_base_version);
}
