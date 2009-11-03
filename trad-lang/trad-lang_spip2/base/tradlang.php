<?php
/**
 * Plugin Tradlang
 * Licence GPL (c) 2009 
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

function tradlang_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['tradlang'] = 'tradlang';
	$interface['table_des_tables']['tradlang_modules'] = 'tradlang_modules';

	return $interface;
}

function tradlang_declarer_tables_principales($tables_principales){
	$spip_tradlang = array(
		"id_tradlang" => "bigint(21) NOT NULL",
		"id" => "varchar(128) NOT NULL default ''",
		"module" => "varchar(32) NOT NULL default 0",
		"lang" => "varchar(16) NOT NULL default ''",
		"str" => "text NOT NULL", 
		"comm" => "text NOT NULL",
		"ts" => "timestamp(14) NOT NULL",
		"status" => "varchar(16) default NULL",
		"traducteur" => "varchar(32) default NULL",
		"md5" => "varchar(32) default NULL",
		"orig" => "tinyint(4) NOT NULL default '0'",
		"date_modif" => "datetime default NULL"
	);
	
	$spip_tradlang_key = array(
		"PRIMARY KEY" => "id_tradlang",
		"UNIQUE" => "id,module,lang",
		"INDEX" => "id",
		"INDEX" => "module",
		"INDEX" => "module,lang"
	);
	
	$tables_principales['spip_tradlang'] = array(
		'field' => &$spip_tradlang,
		'key' => &$spip_tradlang_key);
		
	$spip_tradlang_modules = array(
		"idmodule" => "bigint(21) NOT NULL",
		"nom_module" => "varchar(128) NOT NULL",
		"nom_mod" => "varchar(16) NOT NULL",
		"lang_mere" => "varchar(16) NOT NULL default 'fr'",
		"type_export" => "varchar(16) NOT NULL default 'spip'",
		"dir_lang" => "varchar(255) NOT NULL",
		"lang_prefix" => "varchar(16) NOT NULL");
	
	$spip_tradlang_modules_key = array(
		"PRIMARY KEY" => "idmodule",
		"KEY" => "nom_module",
		"KEY" => "nom_mod",
		"UNIQUE" => "nom_mod"
	);
	
	$tables_principales['spip_tradlang_modules'] = array(
		'field' => &$spip_tradlang_modules,
		'key' => &$spip_tradlang_modules_key);
	
	return $tables_principales;
}

?>
