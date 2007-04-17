<?php
//
// Les tables : 
// 1 table descriptive des zones d'acces
// 2 tables de liens zones<->auteurs et  zones<->rubriques

global $tables_principales;
global $tables_auxiliaires;

$spip_zones = array(
	"id_zone" 	=> "bigint(21) NOT NULL",
	"titre" 	=> "varchar(255) NOT NULL",
	"descriptif" 	=> "text NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_zones_key = array(
	"PRIMARY KEY" => "id_zone");

$tables_principales['spip_zones'] = array(
	'field' => &$spip_zones,
	'key' => &$spip_zones_key);

$spip_zones_auteurs = array(
	"id_zone" 	=> "bigint(21) NOT NULL",
	"id_auteur" 	=> "bigint(21) NOT NULL");

$spip_zones_auteurs_key = array(
	"KEY id_zone" 	=> "id_zone",
	"KEY id_auteur" => "id_auteur");

$tables_auxiliaires['spip_zones_auteurs'] = array(
	'field' => &$spip_zones_auteurs,
	'key' => &$spip_zones_auteurs_key);

$spip_zones_rubriques = array(
	"id_zone" 	=> "bigint(21) NOT NULL",
	"id_rubrique" 	=> "bigint(21) NOT NULL",
	"publique" 	=> "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
	"privee" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL");

$spip_zones_rubriques_key = array(
	"KEY id_zone" 	=> "id_zone",
	"KEY id_rubrique" => "id_rubrique");

$tables_auxiliaires['spip_zones_rubriques'] = array(
	'field' => &$spip_zones_rubriques,
	'key' => &$spip_zones_rubriques_key);

//-- Relations ----------------------------------------------------

global $tables_jointures;
$tables_jointures['spip_auteurs'][] = 'zones_auteurs';
$tables_jointures['spip_zones'][] = 'zones_auteurs';

$tables_jointures['spip_rubriques'][] = 'zones_rubriques';
$tables_jointures['spip_zones'][] = 'zones_rubriques';

//-- Table des tables ----------------------------------------------------

global $table_des_tables;
$table_des_tables['zones']='zones';
$table_des_tables['zones_rubriques']='zones_rubriques';
$table_des_tables['zones_auteurs']='zones_auteurs';

?>