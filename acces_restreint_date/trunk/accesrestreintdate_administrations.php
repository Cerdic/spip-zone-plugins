<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function accesrestreintdate_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_zones_dates'))
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function accesrestreintdate_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_zones_dates");
	effacer_meta($nom_meta_base_version);
}
