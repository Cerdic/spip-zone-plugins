<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Pipelines
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
function emplois_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['offres'] = 'offres';
	$interfaces['table_des_tables']['cvs'] = 'cvs';
	$interfaces['table_des_traitements']['TEXTE_OFFRE'][] = _TRAITEMENT_RACCOURCIS;

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
function emplois_declarer_tables_objets_sql($tables) {

	$tables['spip_offres'] = array(
		'type' => 'offre',
		'principale' => "oui",
		'field'=> array(
			'id_offre'           => 'bigint(21) NOT NULL',
			'id_rubrique'        => 'bigint(21) NOT NULL DEFAULT 0', 
			'id_secteur'         => 'bigint(21) NOT NULL DEFAULT 0', 
			'id_auteur'          => 'bigint(21) NOT NULL DEFAULT 0',
			'id_document_offre'  => 'bigint(21) NOT NULL DEFAULT 0',
			'nom'                => 'text NOT NULL DEFAULT ""',
			'email'              => 'tinytext NOT NULL DEFAULT ""',
			'telephone'          => 'varchar(255) NOT NULL DEFAULT ""',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'emetteur'           => 'text NOT NULL DEFAULT ""',
			'texte_offre'        => 'text NOT NULL DEFAULT ""',
			'date_debut'         => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'date_fin'           => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL', 
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_offre',
			'KEY id_rubrique'    => 'id_rubrique', 
			'KEY id_secteur'     => 'id_secteur', 
			'KEY statut'         => 'statut', 
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date_debut',
		'champs_editables'  => array('nom', 'email', 'telephone', 'titre', 'emetteur', 'texte_offre', 'date_fin'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array("nom" => 8, "titre" => 5),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'emplois:texte_statut_a_valider',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'offre:texte_changer_statut_offre', 
		

	);

	$tables['spip_cvs'] = array(
		'type' => 'cv',
		'principale' => "oui",
		'field'=> array(
			'id_cv'              => 'bigint(21) NOT NULL',
			'id_rubrique'        => 'bigint(21) NOT NULL DEFAULT 0', 
			'id_secteur'         => 'bigint(21) NOT NULL DEFAULT 0', 
			'id_auteur'          => 'bigint(21) NOT NULL DEFAULT 0',
			'id_document_cv'     => 'bigint(21) NOT NULL DEFAULT 0',
			'nom'                => 'text NOT NULL DEFAULT ""',
			'date_debut'         => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"', 
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL', 
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_cv',
			'KEY id_rubrique'    => 'id_rubrique', 
			'KEY id_secteur'     => 'id_secteur', 
			'KEY statut'         => 'statut', 
		),
		'titre' => 'nom AS titre, "" AS lang',
		'date' => 'date_debut',
		'champs_editables'  => array('nom'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'emplois:texte_statut_a_valider',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'cv:texte_changer_statut_cv', 
		

	);

	return $tables;
}