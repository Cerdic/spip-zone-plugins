<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Info SPIP
 *
 * @plugin     Info SPIP
 * @copyright  2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_SPIP\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Info SPIP.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 *
 * @return void
 **/
function info_spip_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Info SPIP.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 *
 * @return void
 **/
function info_spip_vider_tables($nom_meta_base_version) {
	effacer_meta('info_spip');
	effacer_meta($nom_meta_base_version);
}

