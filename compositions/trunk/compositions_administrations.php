<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
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
function compositions_upgrade($nom_meta_base_version,$version_cible){
	include_spip('base/objets');
	$tables_objets = array_keys(lister_tables_objets_sql());
	$maj = array();
	$maj['create'] = array();
	foreach($tables_objets as $table){
		$maj['create'][] = array('sql_alter',"TABLE $table ADD composition varchar(255) DEFAULT '' NOT NULL");
		$maj['create'][] = array('sql_alter',"TABLE $table ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
	}
	$maj['create'][] = array('sql_alter',"TABLE spip_rubriques ADD composition_branche_lock tinyint(1) DEFAULT 0 NOT NULL");

	$maj['0.5.0'] = array();
	foreach($tables_objets as $table){
		$maj['0.5.0'][] = array('sql_alter',"TABLE $table ADD composition varchar(255) DEFAULT '' NOT NULL");
		$maj['0.5.0'][] = array('sql_alter',"TABLE $table ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
	}
	$maj['0.5.0'][] = array('sql_alter',"TABLE spip_rubriques ADD composition_branche_lock tinyint(1) DEFAULT 0 NOT NULL");

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Une fonction pour verifier que les champs sont bien sur tous les objets,
 * appelee lors de la configuration
 * (cas d'un nouvel objet ajoute apres l'install du plugin)
 * 
 * @return void
 */
function compositions_check_upgrade(){
	include_spip('base/objets');
	$tables_objets = array_keys(lister_tables_objets_sql());
	$trouver_table = charger_fonction('trouver_table','base');
	foreach($tables_objets as $table){
		$desc = $trouver_table($table);
		if (!isset($desc['field']['composition']))
			sql_alter("TABLE $table ADD composition varchar(255) DEFAULT '' NOT NULL");
		if (!isset($desc['field']['composition_lock']))
			sql_alter("TABLE $table ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
	}
}

/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function compositions_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');

	include_spip('base/objets');
	$tables_objets = array_keys(lister_tables_objets_sql());
	foreach($tables_objets as $table){
		sql_alter("TABLE $table DROP composition");
		sql_alter("TABLE $table DROP composition_lock");
	}
	sql_alter("TABLE spip_rubriques DROP composition_branche_lock");

	effacer_meta('compositions');
	effacer_meta($nom_meta_base_version);
}
?>