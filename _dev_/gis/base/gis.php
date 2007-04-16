<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

global $tables_principales;
global $tables_auxiliaires;

//taboa gis
$spip_gis = array(
	"id_gis" 	=> "bigint(21) NOT NULL",
	"id_article" => "int(11) NULL NULL",
	"lat" => "float(21)  NULL NULL",
	"lonx" => "float(21)  NULL NULL"
	);
	
$spip_gis_key = array(
	"PRIMARY KEY" => "id_gis",
	"KEY id_article" => "id_article"
	);
$spip_gis_join = array(
	"id_article"=>"id_article"
	);

$tables_principales['spip_gis'] = array(
	'field' => &$spip_gis,
	'key' => &$spip_gis_key,
	'joint' => &$spip_gis_join
	);

//taboa gis_config	
$spip_gis_config = array(
	"id" 	=> "bigint(21) NOT NULL",
	"name" => "varchar(32) NULL NULL",
	"value" => "varchar(255) NULL NULL"
	);
	
$spip_gis_config_key = array(
	"PRIMARY KEY" => "id"
	);

$tables_principales['spip_gis_config'] = array(
	'field' => &$spip_gis_config,
	'key' => &$spip_gis_config_key
	);

//-- Relaci—ns ----------------------------------------------------
global $table_des_tables;
$table_des_tables['gis']='gis';
$table_des_tables['gis_config']='gis_config';
//-- Jointures ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_articles'][]= 'gis';
$tables_jointures['spip_gis'][] = 'articles';

?>