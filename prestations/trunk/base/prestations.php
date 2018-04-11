<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Prestations
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Prestations\Pipelines
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
function prestations_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['prestations'] = 'prestations';
	$interfaces['table_des_tables']['prestations_types'] = 'prestations_types';
	$interfaces['table_des_tables']['prestations_unites'] = 'prestations_unites';

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
function prestations_declarer_tables_objets_sql($tables) {

	$tables['spip_prestations'] = array(
		'type' => 'prestation',
		'principale' => 'oui',
		'field'=> array(
			'id_prestation'          => 'bigint(21) NOT NULL',
			'titre'                  => 'text NOT NULL DEFAULT ""',
			'id_prestations_type'    => 'bigint(21) NOT NULL DEFAULT 0',
			'prix_unitaire_ht'       => 'decimal(20,6) NOT NULL DEFAULT 0',
			'quantite'               => 'decimal(8,4) NOT NULL DEFAULT 0',
			'quantite_relative'      => 'decimal(4,4) NOT NULL DEFAULT 0',
			'quantite_relative_type' => 'varchar(25) not null default ""',
			'quantite_relative_rang' => 'varchar(25) not null default ""',
			'id_prestations_unite'   => 'bigint(21) NOT NULL DEFAULT 0',
			'taxe'                   => 'decimal(4,4) not null default 0',
			'objet'                  => 'varchar(25) NOT NULL DEFAULT ""',
			'id_objet'               => 'bigint(21) NOT NULL DEFAULT 0',
			'rang'                   => 'int(11) NOT NULL DEFAULT 0',
			'maj'                    => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_prestation',
		),
		'titre' => 'titre AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('titre', 'id_prestations_type', 'prix_unitaire_ht', 'quantite', 'quantite_relative', 'quantite_relative_type', 'quantite_relative_rang', 'id_prestations_unite', 'taxe', 'objet', 'id_objet', 'rang'),
		'champs_versionnes' => array('titre', 'id_prestations_type', 'prix_unitaire_ht', 'quantite', 'quantite_relative', 'quantite_relative_type', 'quantite_relative_rang', 'id_prestations_unite', 'taxe', 'objet', 'id_objet', 'rang'),
		'rechercher_champs' => array("titre" => 10),
		'tables_jointures'  => array(),


	);

	$tables['spip_prestations_types'] = array(
		'type' => 'prestations_type',
		'principale' => 'oui',
		'table_objet_surnoms' => array('prestationstype'), // table_objet('prestations_type') => 'prestations_types' 
		'field'=> array(
			'id_prestations_type' => 'bigint(21) NOT NULL',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'prix_unitaire_ht'   => 'decimal(20,6) NOT NULL DEFAULT 0',
			'id_prestations_unite' => 'bigint(21) NOT NULL DEFAULT 0',
			'taxe'               => 'decimal(4,4) not null default 0',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_prestations_type',
		),
		'titre' => 'titre AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('titre', 'prix_unitaire_ht', 'id_prestations_unite', 'taxe'),
		'champs_versionnes' => array('titre', 'prix_unitaire_ht', 'id_prestations_unite', 'taxe'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),


	);

	$tables['spip_prestations_unites'] = array(
		'type' => 'prestations_unite',
		'principale' => 'oui',
		'table_objet_surnoms' => array('prestationsunite'), // table_objet('prestations_unite') => 'prestations_unites' 
		'field'=> array(
			'id_prestations_unite' => 'bigint(21) NOT NULL',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_prestations_unite',
		),
		'titre' => 'titre AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('titre'),
		'champs_versionnes' => array('titre'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),


	);

	return $tables;
}
