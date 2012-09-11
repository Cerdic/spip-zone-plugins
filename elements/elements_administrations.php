<?php
/**
 * Gestion de l'installation et désinstallation
 * 
 * @package SPIP\Elements\Installation
**/

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Installation/maj des tables elements...
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function elements_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_elements'))
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function elements_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table('spip_elements');
	effacer_meta('elements');
	effacer_meta($nom_meta_base_version);
}

?>
