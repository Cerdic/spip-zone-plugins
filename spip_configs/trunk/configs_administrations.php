<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin SPIP Variables
 *
 * @plugin     SPIP Variables
 * @copyright  2017
 * @author     tofulm
 * @licence    GNU/GPL
 * @package    SPIP\Configs\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin SPIP Variables .
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function configs_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_configs')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin SPIP Variables .
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function configs_vider_tables($nom_meta_base_version) {

	// Par sécu, on désactive la suppression de la table
	//sql_drop_table('spip_configs');

	effacer_meta($nom_meta_base_version);
}
