<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     AMAP, Producteurs et Consommateurs associés
 * @copyright  2016
 * @author     Rien
 * @licence    GNU/GPL
 * @package    SPIP\Amappca\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function amappca_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['amap_periodes'] = 'amap_periodes';
	$interfaces['table_des_tables']['amap_distributions'] = 'amap_distributions';

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
function amappca_declarer_tables_objets_sql($tables) {
	$tables['spip_amap_periodes'] = array(
		'type' => 'amap_periode',
		'principale' => "oui", 
		'table_objet_surnoms' => array('amapperiode'), // table_objet('amap_periode') => 'amap_periodes' 
		'field'=> array(
			'id_amap_periode'    => 'bigint(21) NOT NULL',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'date_limite'        => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL', 
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_amap_periode',
			'KEY statut'         => 'statut', 
		),
		'titre' => 'titre AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('titre', 'date_limite'),
		'champs_versionnes' => array('titre', 'date_limite'),
		'rechercher_champs' => array("titre" => 10),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'amap_periode:statut_prepa',
			'prod'     => 'amap_periode:statut_prod',
			'publie'   => 'amap_periode:statut_publie',
			'archive'  => 'amap_periode:statut_archive',
			'poubelle' => 'amap_periode:statut_poubelle',
		),
		'statut_images' => array(
			'prepa'    => 'puce-preparer-8.png',
			'prod'     => 'puce-proposer-8.png',
			'publie'   => 'puce-publier-8.png',
			'archive'  => 'puce-refuser-8.png',
			'poubelle' => 'puce-supprimer-8.png',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prod,prepa',
				'post_date' => 'date_limite', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'amap_periode:texte_changer_statut_amap_periode', 
	);

	$tables['spip_amap_distributions'] = array(
		'type' => 'amap_distribution',
		'principale' => "oui", 
		'table_objet_surnoms' => array('amapdistribution'), // table_objet('amap_distribution') => 'amap_distributions' 
		'field'=> array(
			'id_amap_distribution' => 'bigint(21) NOT NULL',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'id_amap_periode'    => 'bigint(21) NOT NULL DEFAULT 0',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'texte'              => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"', 
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'         => 'id_amap_distribution',
			'KEY id_amap_periode' => 'id_amap_periode',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'id_amap_periode', 'date', 'texte'),
		'champs_versionnes' => array('titre', 'id_amap_periode', 'date', 'texte'),
		'rechercher_champs' => array("titre" => 10, "texte" => 5),
		'tables_jointures'  => array(),
	);
	
	// Ajouter des champs pour les producteurs
	$tables['spip_organisations']['field']['commandes_variables'] = 'int(1) not null default 1';
	$tables['spip_organisations']['field']['paiement'] = 'text NOT NULL DEFAULT ""';
	
	$tables['spip_commandes']['field']['id_amap_periode'] = 'bigint(21) NOT NULL DEFAULT 0';
	$tables['spip_commandes_details']['field']['id_amap_distribution'] = 'bigint(21) NOT NULL DEFAULT 0';
	
	//$tables[]['tables_jointures'] = array('spip_amap_distributions_liens');
	
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
function amappca_declarer_tables_auxiliaires($tables) {
	$tables['spip_amap_distributions_liens'] = array(
		'field' => array(
			'id_amap_distribution' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'             => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'                => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                   => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'              => 'id_amap_distribution,id_objet,objet',
			'KEY id_amap_distribution' => 'id_amap_distribution',
		)
	);

	return $tables;
}
