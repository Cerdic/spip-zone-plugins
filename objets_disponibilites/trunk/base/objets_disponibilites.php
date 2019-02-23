<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Disponibilites objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Objets_disponibilites\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function objets_disponibilites_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['disponibilite_dates'] = 'disponibilite_dates';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function objets_disponibilites_declarer_tables_objets_sql($tables) {

	$tables['spip_disponibilite_dates'] = array(
		'type' => 'disponibilite_date',
		'principale' => 'oui',
		'table_objet_surnoms' => array('disponibilitedate'), // table_objet('disponibilite_date') => 'disponibilite_dates'
		'field'=> array(
			'id_disponibilite_date' => 'bigint(21) NOT NULL',
			'id_disponibilite_date_source' => 'bigint(21) NOT NULL',
			'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
			'disponible'         => 'int(1) NOT NULL DEFAULT 1',
			'date_debut'         => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'date_fin'           => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'horaire'            => 'varchar(3) NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_disponibilite_date',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'disponible', 'date_debut', 'date_fin'),
		'champs_versionnes' => array('titre', 'disponible', 'date_debut', 'date_fin'),
		'rechercher_champs' => array("titre" => 8),
		'tables_jointures'  => array('spip_disponibilite_dates_liens'),


	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function objets_disponibilites_declarer_tables_auxiliaires($tables) {

	$tables['spip_disponibilite_dates_liens'] = array(
		'field' => array(
			'id_disponibilite_date' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_disponibilite_date,id_objet,objet',
			'KEY id_disponibilite_date' => 'id_disponibilite_date',
		)
	);

	return $tables;
}
