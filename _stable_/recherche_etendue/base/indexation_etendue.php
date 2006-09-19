<?php
//
// Les tables : 
// 1 table descriptive des zones d'acces
// 2 tables de liens zones<->auteurs et  zones<->rubriques

global $tables_principales;
global $tables_auxiliaires;
include_spip('base/auxiliaires'); // on va surcharger la def de index

// ajouter une cle primaire sur spip_index
$spip_index_key = array(
		"PRIMARY KEY"	=> "id_table, id_objet, hash",
 		"KEY `hash`"	=> "`hash`",
		"KEY id_objet"	=> "id_objet");
$tables_auxiliaires['spip_index']['key'] = &$spip_index_key;

// definir spip_types_tables
$spip_types_tables = array(
	"id_table" 	=> "bigint(21) NOT NULL",
	"type" 	=> "varchar(100) NOT NULL"
);

$spip_types_tables_key = array(
	"INDEX" => "id_table");

$tables_auxiliaires['spip_types_tables'] = array(
	'field' => &$spip_types_tables,
	'key' => &$spip_types_tables_key);
	
// definir la table de memo des recherches
$spip_recherches = array(
		"mot"	=> "varchar(25) NOT NULL",
		"requete"	=> "varchar(100) NOT NULL",
		"recherches"	=> "INT UNSIGNED NOT NULL",
		"date"	=> "DATE NOT NULL",
		"maj"	=> "TIMESTAMP");

$spip_recherches_key = array(
		"INDEX"	=> "mot");

$tables_auxiliaires['spip_recherches'] = array(
	'field' => &$spip_recherches,
	'key' => &$spip_recherches_key);

//-- Relations ----------------------------------------------------

global $tables_jointures;
$tables_jointures['spip_index'][] = 'types_tables';

global $exceptions_des_tables;
$exceptions_des_tables['index']['type_table']=array('spip_types_tables', 'type');

global $exceptions_des_jointures;
$exceptions_des_jointures['type_table'] = array('spip_types_tables', 'type');

//-- Table des tables ----------------------------------------------------

global $table_des_tables;
$table_des_tables['types_tables']='types_tables';
$table_des_tables['index']='index';

?>