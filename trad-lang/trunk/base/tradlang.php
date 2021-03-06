<?php
/**
 * Plugin Tradlang
 * Licence GPL (c) 2009-2013
 */


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function tradlang_declarer_tables_interfaces($interface) {
	$interface['table_des_tables']['tradlangs'] = 'tradlangs';
	$interface['table_des_tables']['tradlang_modules'] = 'tradlang_modules';
	$interface['table_des_tables']['tradlangs_bilans'] = 'tradlangs_bilans';
	$interface['tables_jointures']['spip_tradlang_modules'][] = 'grappes_liens';
	$interface['table_des_traitements']['PRIORITE'][]= 'typo(supprimer_numero(%s), "TYPO", $connect)';
	$interface['table_des_traitements']['NOM_MOD'][]= 'typo(supprimer_numero(%s), "TYPO", $connect)';
	return $interface;
}

function tradlang_declarer_tables_objets_sql($tables) {
	$tables['spip_auteurs']['field']['langues_preferees'] = "text DEFAULT '' NOT NULL";
	$tables['spip_grappes']['champs_versionnes'][] = 'jointure_tradlang_modules';
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
			"dir_module" => "varchar(255) NOT NULL",
			"dir_lang" => "varchar(255) NOT NULL",
			"texte" => "longtext DEFAULT '' NOT NULL",
			"priorite" => "varchar(128) NOT NULL",
			"gestionnaire" => "varchar(32) DEFAULT 'spip' NOT NULL",
			"lang_prefix" => "varchar(32) NOT NULL",
			"limite_trad" => "tinyint NOT NULL DEFAULT 0",
			"bon_a_pousser" => "tinyint NOT NULL DEFAULT 0"
		),
		'key' => array(
			'PRIMARY KEY' => 'id_tradlang_module',
			'UNIQUE dir_module' => 'dir_module',
			'KEY module' => 'module'
		),
		'join' => array(
			'id_tradlang_module' => 'id_tradlang_module',
			'module' => 'module'
		),
		'rechercher_champs' => array(
			'module' => 8,
			'nom_mod' => 8,
			'texte' => 8,
			'priorite' => 3
		),
		'champs_versionnes' => array('module','nom_mod','texte','lang_mere','priorite','limite_trad')
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
		'champs_editables' => array('str','comm','traducteur','statut','md5'),
		'principale' => 'oui',
		'field'=> array(
			"id_tradlang" => "bigint(21) NOT NULL AUTO_INCREMENT",
			"id_tradlang_module" => "bigint(21) NOT NULL DEFAULT '0'",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"id" => "varchar(128) NOT NULL default ''",
			"module" => "varchar(32) NOT NULL default 0",
			"lang" => "varchar(10) DEFAULT '' NOT NULL",
			"langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
			"str" => "text NOT NULL",
			"comm" => "text NOT NULL",
			"statut" => "varchar(16) NOT NULL default 'NEW'",
			"traducteur" => "varchar(32) default NULL",
			"md5" => "varchar(32) default NULL",
			"orig" => "tinyint(4) NOT NULL default '0'",
			"date_modif" => "datetime default NULL",
			"maj" => "timestamp NOT NULL"
		),
		/**
		 * Laisser le statut ici sinon on n'agit plus sur le pipeline pre_boucle
		 */
		'statut' => array(
			array(
				'champ'=>'statut',
				'publie'=>'OK,NEW,MODIF,RELIRE',
				'previsu'=>'OK,NEW,MODIF,RELIRE',
				'exception'=>'statut')
		),
		'key' => array(
			'PRIMARY KEY' => 'id_tradlang',
			'UNIQUE id_tradlang_module_id_lang' => 'id_tradlang_module,id,lang',
			'KEY id_tradlang_module' => 'id_tradlang_module',
			'KEY id' => 'id',
			'KEY lang' => 'lang',
			'KEY module' => 'module',
			'KEY statut' => 'statut',
			'KEY module_lang' => 'module,lang',
			'KEY id_tradlang_module_lang_statut' => 'id_tradlang_module,lang,statut', // accelere le calcul des bilans
		),
		'join' => array(
			'id_tradlang' => 'id_tradlang',
			'module' =>'module'
		),
		'statut_images' => array(
			'OK' => 'tradlang_statut_ok.png',
			'NEW' => 'tradlang_statut_new.png',
			'MODIF' => 'tradlang_statut_modif.png',
			'RELIRE' => 'tradlang_statut_relire.png'
		),
		'statut_textes_instituer' => 	array(
			'OK' => 'tradlang:str_status_traduit',
			'NEW' => 'tradlang:str_status_new',
			'MODIF' => 'tradlang:str_status_modif',
			'RELIRE' => 'tradlang:str_status_relire'
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

/**
 * Table des bilans spip_tradlangs_bilans
 * @param array $tables_auxiliaires
 * @return array
 */
function tradlang_declarer_tables_auxiliaires($tables_auxiliaires) {

	$spip_tradlangs_bilans = array(
			"id_tradlang_module" => "bigint(21) NOT NULL",
			"module" => "varchar(128) NOT NULL",
			"lang" => "varchar(10) DEFAULT '' NOT NULL",
			"chaines_total"	=> "smallint(5) DEFAULT '0' NOT NULL",
			"chaines_ok"	=> "smallint(5) DEFAULT '0' NOT NULL",
			"chaines_relire" => "smallint(5) DEFAULT '0' NOT NULL",
			"chaines_modif" => "smallint(5) DEFAULT '0' NOT NULL",
			"chaines_new" => "smallint(5) DEFAULT '0' NOT NULL",
			"maj" => "timestamp NOT NULL");

	$spip_tradlangs_bilans_keys = array(
			'UNIQUE id_module_lang' => 'id_tradlang_module,lang',
			'KEY module' => 'module',
			'KEY lang' => 'lang'
	);

	$tables_auxiliaires['spip_tradlangs_bilans'] = array(
		'field' => &$spip_tradlangs_bilans,
		'key' => &$spip_tradlangs_bilans_keys);

	return $tables_auxiliaires;
}
