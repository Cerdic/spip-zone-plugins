<?php

/**
 * Pipeline pour Xiti
 *
 * @plugin     Xiti
 * @copyright  2014
 * @author     Vincent
 * @licence    GNU/GPL
 * @package    SPIP\Xiti\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables xiti
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function xiti_upgrade($nom_meta_base_version, $version_cible){
	
	$maj = array();

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables xiti
 *
 * @param string $nom_meta_base_version
 */
function xiti_vider_tables($nom_meta_base_version) {
	effacer_meta("xiti");
	effacer_meta($nom_meta_base_version);
}

?>