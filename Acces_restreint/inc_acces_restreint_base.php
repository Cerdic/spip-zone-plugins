<?php
//
// Les tables : 
// 1 table descriptive des zones d'acces
// 2 tables de liens zones<->auteurs et  zones<->rubriques
define('_DIR_PLUGIN_ACCES_RESTREINT',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

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
	"id_rubrique" 	=> "bigint(21) NOT NULL");

$spip_zones_rubriques_key = array(
	"KEY id_zone" 	=> "id_zone",
	"KEY id_rubrique" => "id_rubrique");

$tables_auxiliaires['spip_zones_rubriques'] = array(
	'field' => &$spip_zones_rubriques,
	'key' => &$spip_zones_rubriques_key);

//-- Relations ----------------------------------------------------

global $tables_relations;
$tables_relations['auteurs']['id_zone'] = 'zones_auteurs';
$tables_relations['zones']['id_auteur'] = 'zones_auteurs';

$tables_relations['rubriques']['id_zone'] = 'zones_rubriques';
$tables_relations['zones']['id_rubrique'] = 'zones_rubriques';

//-- Table des tables ----------------------------------------------------

global $table_des_tables;
$table_des_tables['zones']='zones';
$table_des_tables['zones_rubriques']='zones_rubriques';
$table_des_tables['zones_auteurs']='zones_auteurs';

?>