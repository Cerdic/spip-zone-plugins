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

/**
 * Desinstallation/suppression des tables GeoIP
 *
 * @param string $nom_meta_base_version
 */
function geoip_vider_tables($nom_meta_base_version) {

	effacer_meta('geoip_version');
	effacer_meta($nom_meta_base_version);
}
