<?php

/**
 * Base pour shortcut_url
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\shortcut_url\base
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Interfaces des tables shortcut_url pour le compilateur
 *
 * @param array $interfaces
 * @return array
 */
function shortcut_url_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['shortcut_urls'] = 'shortcut_urls';
	$interfaces['table_des_tables']['shortcut_urls_logs'] = 'shortcut_urls_logs';
	$interfaces['table_des_tables']['shortcut_urls_bots'] = 'shortcut_urls_bots';

	return $interfaces;
}

function shortcut_url_declarer_tables_objets_sql($tables) {

	$tables['spip_shortcut_urls'] = array(
		'type' 			=> 'shortcut_url',
		'texte_retour' 		=> 'icone_retour',
		'texte_objets' 		=> 'shortcut_url:shortcut_url',
		'texte_objet' 		=> 'shortcut_url:shortcut_url',
		'texte_modifier' 	=> 'shortcut_url:icone_modifier_shortcut_url',
		'texte_creer' 		=> 'shortcut_url:icone_nouveau_shortcut_url',
		'titre' 			=> 'titre',
		'principale' 		=> 'oui',
		'field'=> array(
			'id_shortcut_url' 	=> 'bigint(21) NOT NULL',
			'titre' 			=> "varchar(255) NOT NULL default 'NUL'",
			'description' 		=> "varchar(255) NOT NULL default 'NUL'",
			'url' 				=> "text default '' NOT NULL",
			'ip_address'		=> 'varchar(255) default 0 NOT NULL',
			'date_modif' 		=> "datetime NOT NULL default '0000-00-00 00:00:00'",
			'maj'				=> 'TIMESTAMP',
			'click' 			=> 'varchar(255) NOT NULL default 0'
		),
		'key' => array(
			'PRIMARY KEY' 	=> 'id_shortcut_url',
		),
		'join' => array(
			'id_shortcut_url' => 'id_shortcut_url'
		),
		'rechercher_champs' => array(
			'titre' => 5, 'description' => 2, 'url' => 8
		),
		'champs_editables'  => array('titre','description','url'),
		'champs_versionnes' => array('titre','description','url'),
	);

	$tables['spip_shortcut_urls_logs'] = array(
		'principale' => 'non',
		'field'=> array(
			'id_shortcut_urls_log' 	=> 'bigint(21) NOT NULL',
			'id_shortcut_url' 	=> 'bigint(21) NOT NULL',
			'date_modif' 		=> 'TIMESTAMP',
			'shorturl'			=> "varchar(200) NOT NULL default 'NUL'",
			'referrer' 			=> "varchar(200) NOT NULL default 'NUL'",
			'user_agent' 		=> "varchar(255) NOT NULL default 'NUL'",
			'ip_address' 		=> 'varchar(41) default 0 NOT NULL',
			'country_code' 		=> 'char(2) default 0 NOT NULL',
			'humain' 			=> "varchar(3) default '' NOT NULL"
		),
		'key' => array(
			'PRIMARY KEY'	=> 'id_shortcut_urls_log',
		)
	);

	$tables['spip_shortcut_urls_bots'] = array(
		'principale' => 'non',
		'field'=> array(
			'id_shortcut_urls_bot' 	=> 'bigint(21) NOT NULL',
			'id_shortcut_url' 	=> 'bigint(21) NOT NULL',
			'date_modif' 		=> 'TIMESTAMP',
			'referrer' 			=> "varchar(200) NOT NULL default ''",
			'user_agent' 		=> "varchar(255) NOT NULL default ''",
			'ip_address' 		=> 'varchar(41) default 0 NOT NULL'
		),
		'key' => array(
			'PRIMARY KEY'	=> 'id_shortcut_urls_bot'
		)
	);

	$tables[]['tables_jointures'][] = 'shortcut_urls';
	$tables[]['champs_versionnes'][] = 'jointure_shortcut_url';

	return $tables;
}
