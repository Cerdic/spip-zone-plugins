<?php

/**
 * Administrations pour GeoIP
 *
 * @plugin     GeoIP
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\GeoIP\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function geoip_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array();

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables GeoIP
 *
 * @param string $nom_meta_base_version
 */
function geoip_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	effacer_meta('geoip_version');
	effacer_meta($nom_meta_base_version);
}
