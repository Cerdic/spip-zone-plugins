<?php
#include_spip('base/serial');

function geographie_declarer_tables_principales($tables_principales){
	$spip_geo_pays = array(
			"id_pays"	=> "smallint NOT NULL",
			"code"	=> "varchar(2) default '' NOT NULL",
			"nom"	=> "text DEFAULT '' NOT NULL",
	);
	$spip_geo_pays_key = array(
			"PRIMARY KEY"		=> "id_pays"
	);
	$spip_geo_regions = array(
			"id_region"	=> "smallint NOT NULL",
			"id_pays"	=> "smallint NOT NULL",
			"nom"	=> "tinytext DEFAULT '' NOT NULL",
	);
	$spip_geo_regions_key = array(
			"PRIMARY KEY"		=> "id_region"
	);
	$spip_geo_departements = array(
			"id_departement"	=> "smallint NOT NULL",
			"abbr"	=> "varchar(5) default '' NOT NULL",
			"id_region"	=> "smallint NOT NULL",
			"nom"	=> "tinytext DEFAULT '' NOT NULL",
	);
	$spip_geo_departements_key = array(
			"PRIMARY KEY"		=> "id_departement"
	);

	$spip_geo_arrondissements = array(
			"id_arrondissement"	=> "bigint(21) NOT NULL",
			"id_departement"	=> "smallint NOT NULL",
			"nom"	=> "tinytext DEFAULT '' NOT NULL",
			"id_commune"	=> "bigint(21) NOT NULL",
			"population"	=> "integer DEFAULT 0",
			"superficie"	=> "integer DEFAULT 0",
			"densite"	=> "integer DEFAULT 0",
			"nb_cantons"	=> "integer DEFAULT 0",
			"nb_communes"	=> "integer DEFAULT 0",
	);
	$spip_geo_arrondissements_key = array(
			"PRIMARY KEY"		=> "id_arrondissement"
	);

	$spip_geo_communes = array(
			"id_commune"	=> "bigint(21) NOT NULL",
			"insee"	=> "char(6) default '' NOT NULL",
			"id_departement"	=> "smallint NOT NULL",
			"id_pays"	=> "smallint NOT NULL",
			"code_postal"	=> "char(5) default '' NOT NULL",
			"nom"	=> "tinytext DEFAULT '' NOT NULL",
			"longitude"	=> "varchar(15) default '' NOT NULL",
			"latitude"	=> "varchar(15) default '' NOT NULL",
	);
	$spip_geo_communes_key = array(
			"PRIMARY KEY"		=> "id_commune",
			"INDEX insee"		=> "insee",
			"INDEX id_pays"		=> "id_pays"
	);

	$tables_principales['spip_geo_pays'] = array('field'=>&$spip_geo_pays,'key'=>$spip_geo_pays_key);
	$tables_principales['spip_geo_regions'] = array('field'=>&$spip_geo_regions,'key'=>$spip_geo_regions_key);
	$tables_principales['spip_geo_departements'] = array('field'=>&$spip_geo_departements,'key'=>$spip_geo_departements_key);
	$tables_principales['spip_geo_arrondissements'] = array('field'=>&$spip_geo_arrondissements,'key'=>$spip_geo_arrondissements_key);
	$tables_principales['spip_geo_communes'] = array('field'=>&$spip_geo_communes,'key'=>$spip_geo_communes_key);

	return $tables_principales;
}

function geographie_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['geo_pays'] = 'geo_pays';
	$interface['table_des_tables']['geo_regions'] = 'geo_regions';
	$interface['table_des_tables']['geo_departements'] = 'geo_departements';
	$interface['table_des_tables']['geo_arrondissements'] = 'geo_arrondissements';
	$interface['table_des_tables']['geo_communes'] = 'geo_communes';


	return $interface;
}


function geographie_lister_tables_noexport($liste){
	$liste[] = 'spip_geo_communes';
	$liste[] = 'spip_geo_arrondissements';
	$liste[] = 'spip_geo_departements';
	$liste[] = 'spip_geo_regions';
	$liste[] = 'spip_geo_pays';
	return $liste;
}

global $IMPORT_tables_noerase;
$IMPORT_tables_noerase[]='spip_geo_communes';
$IMPORT_tables_noerase[]='spip_geo_arrondissements';
$IMPORT_tables_noerase[]='spip_geo_departements';
$IMPORT_tables_noerase[]='spip_geo_regions';
$IMPORT_tables_noerase[]='spip_geo_pays';