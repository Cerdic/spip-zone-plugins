<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package SPIP\pb_selectione\Installation
**/
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}



/**
 * Installation du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function pb_selection_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	$maj['create'] = array(array('maj_tables', array('spip_pb_selection')));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Désinstallation du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function pb_selection_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_pb_selection");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

