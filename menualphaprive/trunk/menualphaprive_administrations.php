<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Menu privé alphabétique
 *
 * @plugin     Menu privé alphabétique
 * @copyright  2017
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Menualphaprive\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Menu privé alphabétique.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 **/
function menualphaprive_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	// on augmente la taille du champ 'prefs' de tinytext à text.
	$maj['create'] = array(array('sql_alter', 'TABLE spip_auteurs CHANGE prefs prefs TEXT'));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Menu privé alphabétique.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 **/
function menualphaprive_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
