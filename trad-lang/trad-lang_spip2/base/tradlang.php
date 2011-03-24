<?php
/**
 * Plugin Tradlang
 * Licence GPL (c) 2009 
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

function tradlang_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['tradlang'] = 'tradlang';
	$interface['table_des_tables']['tradlang_modules'] = 'tradlang_modules';
	$interface['table_titre']['tradlang'] = "CONCAT(module,' : ',id,' ','[',lang,']') AS titre";
	return $interface;
}

function tradlang_declarer_tables_objets_sql($tables){
	$tables['spip_tradlang_modules'] = array(
		'texte_retour' => 'icone_retour',
		'titre' => 'nom_module',
		'field'=> array(
			"idmodule" => "bigint(21) NOT NULL",
			"nom_module" => "varchar(128) NOT NULL",
			"nom_mod" => "varchar(16) NOT NULL",
			"lang_mere" => "varchar(16) NOT NULL default 'fr'",
			"type_export" => "varchar(16) NOT NULL default 'spip'",
			"dir_lang" => "varchar(255) NOT NULL",
			"texte" => "longtext DEFAULT '' NOT NULL",
			"lang_prefix" => "varchar(16) NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY" => "idmodule",
			"KEY" => "nom_module",
			"UNIQUE" => "nom_mod"
		),
		'join' => array(
			"idmodule"=>"idmodule",
			"module"=>"nom_module"
		),
		'rechercher_champs' => array(
		  'nom_module' => 8
		),
		'champs_versionnes' => array('nom_module','nom_mod', 'lang_mere')
	);

	$tables['spip_tradlang'] = array(
		'texte_retour' => 'icone_retour',
		'titre' => 'id, lang',
		'date' => 'date_modif',
		'field'=> array(
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
			"date_modif" => "datetime default NULL",
			"maj"	=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_tradlang",
			"UNIQUE" => "id,module,lang",
			"INDEX" => "id",
			"INDEX" => "module",
			"INDEX" => "module,lang"
		),
		'join' => array(
			"id_tradlang"=>"id_tradlang",
			"module"=>"module"
		),
		'rechercher_champs' => array(
		  'str' => 8, 'comm' => 4
		),
		'champs_versionnes' => array('str','comm', 'status')
	);
	return $tables;
}

?>
