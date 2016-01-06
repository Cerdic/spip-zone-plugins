<?php

/**
 * Déclarations relatives à la base de données.
 *
 * @plugin     Unsplash
 *
 * @copyright  2015-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des alias de tables et filtres automatiques de champs.
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interfaces Déclarations d'interface pour le compilateur
 *
 * @return array Déclarations d'interface pour le compilateur
 */
function unsplash_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['unsplash'] = 'unsplash';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux.
 *
 * @pipeline declarer_tables_objets_sql
 *
 * @param array $tables Description des tables
 *
 * @return array Description complétée des tables
 */
function unsplash_declarer_tables_objets_sql($tables) {
	$tables['spip_unsplash'] = array(
		'type' => 'unsplash',
		'principale' => 'oui',
		'table_objet_surnoms' => array('unsplash'), // table_objet('unsplash') => 'unsplash'
		'field' => array(
			'id_unsplash' => 'bigint(21) NOT NULL',
			'filename' => "varchar(30) NOT NULL DEFAULT ''",
			'format' => "varchar(5) NOT NULL DEFAULT ''",
			'width' => "varchar(5) NOT NULL DEFAULT ''",
			'height' => "varchar(5) NOT NULL DEFAULT ''",
			'author' => "tinytext NOT NULL DEFAULT ''",
			'author_url' => "text NOT NULL DEFAULT ''",
			'post_url' => "text NOT NULL DEFAULT ''",
			'id_objet' => 'bigint(21) NOT NULL',
			'objet' => "varchar(25) NOT NULL DEFAULT ''",
			'mode' => "ENUM('document', 'normal', 'survol') DEFAULT 'document' NOT NULL",
			'date_ajout' => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'maj' => 'TIMESTAMP',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_unsplash',
		),
		'titre' => "filename AS titre, '' AS lang",
		'date' => 'date_ajout',
		'champs_editables' => array('filename', 'format', 'width', 'height', 'author', 'author_url', 'post_url', 'mode'),
		'champs_versionnes' => array('format', 'width', 'height', 'author', 'author_url', 'post_url', 'mode'),
		'rechercher_champs' => array('author' => 6, 'author_url' => 5),
		'tables_jointures' => array(),
		'page' => false,
		'url_voir' => 'unsplash_voir',

	);

	return $tables;
}
