<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Spipr-Dane Config
 *
 * @plugin     Spipr-Dane Config
 * @copyright  2017
 * @author     Webmestre DANE
 * @licence    GNU/GPL
 * @package    SPIP\Sdc\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Spipr-Dane Config.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function sdc_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Spipr-Dane Config.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function sdc_vider_tables($nom_meta_base_version) {
	include_spip('inc/config');
	effacer_config('sdc');
	if (is_file(_DIR_SITE."squelettes/css/colors.less")) unlink(_DIR_SITE."squelettes/css/colors.less");
	if (is_file(_DIR_SITE."squelettes/css/typography.less")) unlink(_DIR_SITE."squelettes/css/typography.less");

	effacer_meta($nom_meta_base_version);
}
