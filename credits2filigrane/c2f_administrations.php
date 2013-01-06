<?php
/*
 * Plugin credits2filigrane
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Upgrade des tables
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function c2f_upgrade($nom_meta_base_version,$version_cible){
	include_spip('base/objets');
	$maj = array();
	$maj['0.1.1'] = array();
	$maj['0.1.1'][] = array('sql_alter',"TABLE spip_documents ADD filigrane varchar(255) DEFAULT '' NOT NULL");
	$maj['0.1.1'][] = array('sql_update', 'spip_documents', array('filigrane'=>'credits'), array("credits !=''", "extension IN ('jpg','gif','png')"));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function c2f_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');

	include_spip('base/objets');
	sql_alter("TABLE spip_documents DROP filigrane");

	effacer_meta('c2f');
	effacer_meta($nom_meta_base_version);
}
?>