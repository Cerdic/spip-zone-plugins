<?php
//
// Les tables : 
// 1 table descriptive des zones d'acces
// 2 tables de liens zones<->auteurs et  zones<->rubriques

global $tables_principales;
global $tables_auxiliaires;

$spip_types_tables = array(
	"id_table" 	=> "bigint(21) NOT NULL",
	"type" 	=> "varchar(100) NOT NULL"
);

$$spip_types_tables_key = array(
	"PRIMARY KEY" => "id_table");

$tables_auxiliaires['spip_types_tables'] = array(
	'field' => &$spip_types_tables,
	'key' => &$spip_zones_rubriques_key);

//-- Relations ----------------------------------------------------

global $tables_jointures;
$tables_jointures['spip_index'][] = 'types_tables';

//-- Table des tables ----------------------------------------------------

global $table_des_tables;
//$table_des_tables['types_tables']='types_tables';

?>