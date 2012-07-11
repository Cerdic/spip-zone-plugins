<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin 
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');
include_spip('base/motus');

/**
 * Installation du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function motus_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	cextras_api_upgrade(motus_declarer_champs_extras(), $maj['create']);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Dénstallation du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function motus_vider_tables($nom_meta_base_version) {
	cextras_api_vider_tables(motus_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}

?>
