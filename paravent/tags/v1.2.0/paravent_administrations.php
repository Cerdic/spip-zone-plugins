<?php
/**
 * Plugin Paravent
 * (c) 2013 Scribe
 * Licence GNU/GPL
 */


/**
 * Installation/maj base
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function paravent_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Installation/maj base
 *
 * @param string $nom_meta_base_version
 */
function paravent_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	effacer_meta('paravent');
}

?>