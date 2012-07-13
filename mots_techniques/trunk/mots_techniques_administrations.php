<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package Mots_Techniques\Installation
**/

include_spip('inc/cextras');
include_spip('base/mots_techniques');

/**
 * Installation du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function mots_techniques_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();

	cextras_api_upgrade(mots_techniques_declarer_champs_extras(), $maj['create']);

	$maj['0.2'][] = array('sql_alter', "TABLE spip_groupes_mots DROP affiche_formulaire");

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
function mots_techniques_vider_tables($nom_meta_base_version) {
	cextras_api_vider_tables(mots_techniques_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}

?>
