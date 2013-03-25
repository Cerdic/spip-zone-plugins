<?php
/**
 * Plugin Tradlang
 * Licence GPL (c) 2009-2012 
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

function tradlang_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['tradlangs'] = 'tradlangs';
	$interface['table_des_tables']['tradlang_modules'] = 'tradlang_modules';
	$interface['table_des_traitements']['PRIORITE'][]= 'typo(supprimer_numero(%s), "TYPO", $connect)';
	return $interface;
}

function tradlang_declarer_tables_objets_sql($tables){
	$tables['spip_auteurs']['field']['langues_preferees'] = "text DEFAULT '' NOT NULL";
	$tables['spip_tradlang_modules'] = array(
		'texte_retour' => 'icone_retour',
		'texte_objet' => 'tradlang:titre_tradlang_module',
		'texte_objets' => 'tradlang:titre_tradlang_modules',
		'texte_modifier' => 'tradlang:icone_modifier_tradlang_module',
		'texte_logo_objet' => 'tradlang:titre_logo_tradlang_module',
		'info_aucun_objet'=> 'tradlang:info_aucun_tradlang_module',
		'info_1_objet' => 'tradlang:info_1_tradlang_module',
		'info_nb_objets' => 'tradlang:info_nb_tradlang_module',
		'titre' => "nom_mod AS titre, '' AS lang",
		'principale' => 'oui',
		'field'=> array(
			"id_tradlang_module" => "bigint(21) NOT NULL AUTO_INCREMENT",
			"module" => "varchar(128) NOT NULL",
			"nom_mod" => "text DEFAULT '' NOT NULL",
			"lang_mere" => "varchar(16) NOT NULL default 'fr'",
			"type_export" => "varchar(16) NOT NULL default 'spip'",
			"dir_lang" => "varchar(255) NOT NULL",
			"texte" => "longtext DEFAULT '' NOT NULL",
			"priorite" => "varchar(128) NOT NULL",
			"gestionnaire" => "text DEFAULT 'spip' NOT NULL",
			"lang_prefix" => "varchar(32) NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY" => "id_tradlang_module",
			"UNIQUE" => "module"
		),
		'join' => array(
			"id_tradlang_module"=>"id_tradlang_module",
			"module"=>"module"
		),
		'rechercher_champs' => array(
			'module' => 8,
			'nom_mod' => 8,
			'texte' => 8,
			'priorite' => 3
		),
		'champs_versionnes' => array('module','nom_mod','texte','lang_mere','priorite')
	);

	$tables['spip_tradlangs'] = array(
		'page'=>'tradlang',
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'tradlang:titre_tradlang_chaines',
		'texte_modifier' => 'tradlang:icone_modifier_tradlang',
		'info_aucun_objet'=> 'tradlang:info_aucun_tradlang',
		'info_1_objet' => 'tradlang:info_1_tradlang',
		'info_nb_objets' => 'tradlang:info_nb_tradlang',
		'date' => 'date_modif',
		'champs_editables' => array('str','comm','traducteur','statut'),
		'principale' => 'oui',
		'field'=> array(
			"id_tradlang" => "bigint(21) NOT NULL AUTO_INCREMENT",
			"id_tradlang_module" => "bigint(21) NOT NULL DEFAULT '0'",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"id" => "varchar(128) NOT NULL default ''",
			"module" => "varchar(32) NOT NULL default 0",
			"lang" => "varchar(16) NOT NULL default ''",
			"langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
			"str" => "text NOT NULL", 
			"comm" => "text NOT NULL",
			"statut" => "varchar(16) NOT NULL default 'NEW'",
			"traducteur" => "varchar(32) default NULL",
			"md5" => "varchar(32) default NULL",
			"orig" => "tinyint(4) NOT NULL default '0'",
			"date_modif" => "datetime default NULL",
			"maj" => "timestamp(14) NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY" => "id_tradlang",
			"UNIQUE" => "id,module,lang",
			"INDEX" => "id",
			"INDEX" => "module",
			"INDEX" => "module,lang",
			"INDEX" => "id_tradlang_module",
			"INDEX" => "statut"
		),
		'join' => array(
			"id_tradlang"=>"id_tradlang",
			"module"=>"module"
		),

		'statut_images' => array(
			'OK' => 'tradlang_statut_ok.png',
			'NEW' => 'tradlang_statut_new.png',
			'MODIF' => 'tradlang_statut_modif.png'
		),
		'statut_textes_instituer' => 	array(
			'OK' => 'tradlang:texte_statut_ok',
			'NEW' => 'tradlang:texte_statut_new',
			'MODIF' => 'tradlang:texte_statut_modif',
		),
		'rechercher_champs' => array(
			'id' => 8,
			'str' => 8,
			'comm' => 4
		),
		'champs_versionnes' => array('str','comm','traducteur','statut')
	);
	return $tables;
}

?>