<?php
/*
 * Plugin Polyhierarchie
 * (c) 2009-2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Declaration des tables principales
 *
 * @param array $tables_principales
 * @return array
 */
function polyhier_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_rubriques_liens = array(
		"id_parent"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL");
	$spip_rubriques_liens_key = array(
			"PRIMARY KEY"		=> "id_parent,id_objet,objet",
			"KEY id_parent"	=> "id_parent",
			"KEY id_objet"	=> "id_objet",
			"KEY objet"	=> "objet",
	);

	$tables_auxiliaires['spip_rubriques_liens'] = array(
	'field' => &$spip_rubriques_liens,
	'key' => &$spip_rubriques_liens_key);
	
	return $tables_auxiliaires;
}

/**
 * Upgrade des tables
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function polyhier_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_rubriques_liens')),
	);

	$maj['0.2.0'] = array(
		array('maj_tables',array('spip_rubriques_liens')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function polyhier_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table("spip_rubriques_liens");
	effacer_meta($nom_meta_base_version);
}
?>