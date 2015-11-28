<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Data FFE
 *
 * @plugin     Data FFE
 * @copyright  2015
 * @author     Jacques
 * @licence    GNU/GPL
 * @package    SPIP\Ffedata\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
/** Fonction d'installation et de mise à jour du plugin Data FFE. **/
function ffedata_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/** Fonction de désinstallation du plugin Data FFE.**/
function ffedata_vider_tables($nom_meta_base_version) {
		effacer_meta($nom_meta_base_version);
}