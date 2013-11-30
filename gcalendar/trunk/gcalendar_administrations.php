<?php
/**
 * Plugin Gcalendar
 * (c) 2012 Gilles Quiniou
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function gcalendar_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function gcalendar_vider_tables($nom_meta_base_version) {


	effacer_meta($nom_meta_base_version);
}

?>