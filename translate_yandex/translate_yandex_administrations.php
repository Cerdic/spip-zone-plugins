<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Translate Yandex
 *
 * @plugin     Translate Yandex
 * @copyright  2018
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Translate_yandex\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Translate Yandex.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function translate_yandex_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Translate Yandex.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function translate_yandex_vider_tables($nom_meta_base_version) {


	effacer_meta($nom_meta_base_version);
}
