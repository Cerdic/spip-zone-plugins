<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package SPIP\Motus\Installation
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
	$maj['1.0.1'] = array(array('motus_update_rubrique_on'));

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
function motus_vider_tables($nom_meta_base_version) {
	cextras_api_vider_tables(motus_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}

/**
 * Mise à jour du type du champ rubrique_on de varchar(255) à text
 */
function motus_update_rubrique_on(){
	sql_alter("TABLE spip_groupes_mots CHANGE rubriques_on rubriques_on text DEFAULT '' NOT NULL");
}