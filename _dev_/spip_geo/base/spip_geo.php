<?php
/*
 * SPIP_Geo
 * Avoir a disposition dans spip une liste de continent / pays / ville utilisable par les autres plugins facilement...
 *
 * Auteurs :
 * Quentin Drouet
 * 2007 - Distribue sous licence GNU/GPL
 *
 */

//
// Structure des tables
//

global $tables_principales;
global $tables_auxiliaires;

$spip_geo_continent = array(
	"id_continent" 	=> "bigint(21) NOT NULL",
	"nom" 	=> "varchar(255) NOT NULL",
	"latitude" 	=> "text",
	"longitude" 	=> "text",
	"zoom" 	=> "text",
	"maj" 		=> "TIMESTAMP");

$spip_geo_continent_key = array(
	"PRIMARY KEY" => "id_continent");
	
$spip_geo_pays = array(
	"id_pays" 	=> "bigint(21) NOT NULL",
	"id_continent" 	=> "bigint(21) NOT NULL",
	"nom" 	=> "varchar(255) NOT NULL",
	"latitude" 	=> "text",
	"longitude" 	=> "text",
	"zoom" 	=> "text",
	"indic_tel" => "text",
	"maj" 		=> "TIMESTAMP");

$spip_geo_pays_key = array(
	"KEY id_continent" 	=> "id_continent",
	"KEY id_pays" 	=> "id_pays");
	
$spip_geo_ville = array(
	"id_ville" 	=> "bigint(21) NOT NULL",
	"id_continent" 	=> "bigint(21) NOT NULL",
	"id_pays" 	=> "bigint(21) NOT NULL",
	"nom" 	=> "varchar(255) NOT NULL",
	"latitude" 	=> "text",
	"longitude" => "text",
	"zoom" 	=> "text",
	"maj" 		=> "TIMESTAMP");

$spip_geo_ville_key = array(
	"KEY id_continent" 	=> "id_continent",
	"KEY id_pays" 	=> "id_pays",
	"KEY id_ville" 	=> "id_ville");

$tables_principales['spip_geo_continent'] = array(
	'field' => &$spip_geo_continent,
	'key' => &$spip_geo_continent_key);

$tables_principales['spip_geo_pays'] = array(
	'field' => &$spip_geo_pays,
	'key' => &$spip_geo_pays_key);
	
$tables_principales['spip_geo_ville'] = array(
	'field' => &$spip_geo_ville,
	'key' => &$spip_geo_ville_key);

//-- Relations ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_geo_continent'][] = 'geo_pays';
$tables_jointures['spip_geo_continent'][] = 'geo_ville';
$tables_jointures['spip_geo_pays'][] = 'geo_ville';

global $table_des_tables;
$table_des_tables['geo_continent']='geo_continent';
$table_des_tables['geo_pays'] = 'geo_pays';
$table_des_tables['geo_ville'] = 'geo_ville';

?>