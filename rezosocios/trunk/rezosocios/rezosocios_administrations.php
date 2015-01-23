<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables rezosocios...
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function rezosocios_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_rezosocios','spip_rezosocios_liens'))
	);
	$maj['0.2.0'] = array(
		array('maj_tables', array('spip_rezosocios')),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Desinstallation/suppression des tables rezosocios
 *
 * @param string $nom_meta_base_version
 */
function rezosocios_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_rezosocios");
	sql_drop_table("spip_rezosocios_liens");
	
	effacer_meta($nom_meta_base_version);
}

?>
