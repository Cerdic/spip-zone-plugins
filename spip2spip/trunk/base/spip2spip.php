<?php
/**
 * Plugin Spip2spip
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function spip2spip_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['spip2spips'] = 'spip2spips';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 */
function spip2spip_declarer_tables_objets_sql($tables) {

	$tables['spip_spip2spips'] = array(
		'type' => 'spip2spip',
		'principale' => "oui",
		'field'=> array(
			"id_spip2spip"       => "bigint(21) NOT NULL",        
			"site_titre"         => "varchar(255) NOT NULL DEFAULT ''",
			"site_rss"           => "varchar(255) NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_spip2spip"
		),
		 'titre' => "site_titre AS titre, '' AS lang",
		// 'date' => "",
        'page' => false,
        'editable' => 'oui',
		'champs_editables'  => array('site_titre', 'site_rss'),
		'champs_versionnes' => array('site_titre', 'site_rss'),
		'rechercher_champs' => array('site_titre' => 5),
		'tables_jointures'  => array(), 
	);

	return $tables;
}