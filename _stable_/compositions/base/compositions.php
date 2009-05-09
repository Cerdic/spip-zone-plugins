<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

/**
 * Declaration des tables principales
 *
 * @param array $tables_principales
 * @return array
 */
function compositions_declarer_tables_principales($tables_principales){
	
	$tables_principales['spip_rubriques']['field']['id_article_accueil'] = "bigint(21) DEFAULT '0' NOT NULL";
	$tables_principales['spip_articles']['field']['composition'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_rubriques']['field']['composition'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_auteurs']['field']['composition'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_breves']['field']['composition'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_mots']['field']['composition'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_syndic']['field']['composition'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_articles']['field']['composition_lock'] = "tinyint(1) DEFAULT 0 NOT NULL";
	$tables_principales['spip_rubriques']['field']['composition_lock'] = "tinyint(1) DEFAULT 0 NOT NULL";
	$tables_principales['spip_auteurs']['field']['composition_lock'] = "tinyint(1) DEFAULT 0 NOT NULL";
	$tables_principales['spip_breves']['field']['composition_lock'] = "tinyint(1) DEFAULT 0 NOT NULL";
	$tables_principales['spip_mots']['field']['composition_lock'] = "tinyint(1) DEFAULT 0 NOT NULL";
	$tables_principales['spip_syndic']['field']['composition_lock'] = "tinyint(1) DEFAULT 0 NOT NULL";
	return $tables_principales;
}

/**
 * Upgrade des tables
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function compositions_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/abstract_sql');
			sql_alter("TABLE spip_articles ADD composition varchar(255) DEFAULT '' NOT NULL");
			sql_alter("TABLE spip_rubriques ADD composition varchar(255) DEFAULT '' NOT NULL");
			sql_alter("TABLE spip_rubriques ADD id_article_accueil bigint(21) DEFAULT '0' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version="0.1",'non');
		}
		if (version_compare($current_version,'0.2.0','<')){
			sql_alter("TABLE spip_auteurs ADD composition varchar(255) DEFAULT '' NOT NULL");
			sql_alter("TABLE spip_breves ADD composition varchar(255) DEFAULT '' NOT NULL");
			sql_alter("TABLE spip_mots ADD composition varchar(255) DEFAULT '' NOT NULL");
			sql_alter("TABLE spip_syndic ADD composition varchar(255) DEFAULT '' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version="0.2.0",'non');
		}
		if (version_compare($current_version,'0.3.0','<')){
			sql_alter("TABLE spip_articles ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
			sql_alter("TABLE spip_rubriques ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
			sql_alter("TABLE spip_auteurs ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
			sql_alter("TABLE spip_breves ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
			sql_alter("TABLE spip_mots ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
			sql_alter("TABLE spip_syndic ADD composition_lock tinyint(1) DEFAULT 0 NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version="0.3.0",'non');
		}
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
	sql_alter("TABLE spip_rubriques DROP id_article_accueil");
	sql_alter("TABLE spip_articles DROP composition");
	sql_alter("TABLE spip_rubriques DROP composition");
	sql_alter("TABLE spip_auteurs DROP composition");
	sql_alter("TABLE spip_breves DROP composition");
	sql_alter("TABLE spip_mots DROP composition");
	sql_alter("TABLE spip_syndic DROP composition");
	sql_alter("TABLE spip_articles DROP composition_lock");
	sql_alter("TABLE spip_rubriques DROP composition_lock");
	sql_alter("TABLE spip_auteurs DROP composition_lock");
	sql_alter("TABLE spip_breves DROP composition_lock");
	sql_alter("TABLE spip_mots DROP composition_lock");
	sql_alter("TABLE spip_syndic DROP composition_lock");
	effacer_meta($nom_meta_base_version);
}
?>