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

if (!defined("_ECRIRE_INC_VERSION")) return;

function spip_geo_declarer_tables_interfaces($interface){

	$interface['tables_jointures']['spip_geo_continent'][] = 'geo_pays';
	$interface['tables_jointures']['spip_geo_continent'][] = 'geo_ville';
	$interface['tables_jointures']['spip_geo_pays'][] = 'geo_ville';

	
	//-- Table des tables ----------------------------------------------------
	
	$interface['table_des_tables']['geo_continent']='geo_continent';
	$interface['table_des_tables']['geo_pays']='geo_pays';
	$interface['table_des_tables']['geo_ville']='geo_ville';

	return $interface;
}

function spip_geo_declarer_tables_principales($tables_principales){
	$spip_geo_continent = array(
		"id_continent" 	=> "SMALLINT NOT NULL",
		"continent" 	=> "varchar(255) NOT NULL",
		"code_onu" 	=> "SMALLINT NOT NULL",
		"latitude" 	=> "text",
		"longitude" 	=> "text",
		"zoom" 	=> "text",
		"maj" 		=> "TIMESTAMP");
	
	$spip_geo_continent_key = array(
		"PRIMARY KEY" => "id_continent");
		
	$spip_geo_pays = array(
		"id_pays" 	=> "SMALLINT NOT NULL",
		"id_continent" 	=> "SMALLINT NOT NULL",
		"pays" 	=> "varchar(255) NOT NULL",
		"code_iso" => "text",
		"latitude" 	=> "text",
		"longitude" => "text",
		"zoom" 	=> "text",
		"indic_tel" => "text",
		"maj" 		=> "TIMESTAMP");
	
	$spip_geo_pays_key = array(
		"KEY id_continent" 	=> "id_continent",
		"PRIMARY KEY id_pays" 	=> "id_pays");
		
	$spip_geo_ville = array(
		"id_ville" 	=> "int NOT NULL",
		"id_continent" 	=> "SMALLINT NOT NULL",
		"id_pays" 	=> "SMALLINT NOT NULL",
		"ville" 	=> "varchar(255) NOT NULL",
		"latitude" 	=> "text",
		"longitude" => "text",
		"zoom" 	=> "text",
		"maj" 		=> "TIMESTAMP");
	
	$spip_geo_ville_key = array(
		"KEY id_continent" 	=> "id_continent",
		"KEY id_pays" 	=> "id_pays",
		"PRIMARY KEY id_ville" 	=> "id_ville");
	
	$tables_principales['spip_geo_continent'] = array(
		'field' => &$spip_geo_continent,
		'key' => &$spip_geo_continent_key);
	
	$tables_principales['spip_geo_pays'] = array(
		'field' => &$spip_geo_pays,
		'key' => &$spip_geo_pays_key);
		
	$tables_principales['spip_geo_ville'] = array(
		'field' => &$spip_geo_ville,
		'key' => &$spip_geo_ville_key);
		
	return $tables_principales;
}


?>