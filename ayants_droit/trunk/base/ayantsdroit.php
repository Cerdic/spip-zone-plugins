<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Ayants droit
 * @copyright  2016
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Ayantsdroit\Pipelines
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
function ayantsdroit_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['droits_ayants'] = 'droits_ayants';
	$interfaces['table_des_tables']['droits_contrats'] = 'droits_contrats';
	
	$interfaces['table_des_traitements']['ADRESSE'][]= _TRAITEMENT_RACCOURCIS;
	$interfaces['table_des_traitements']['CREDITS']['droits_contrats']= _TRAITEMENT_RACCOURCIS;
	
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
function ayantsdroit_declarer_tables_objets_sql($tables) {
	$tables['spip_droits_ayants'] = array(
		'type' => 'droits_ayant',
		'principale' => "oui", 
		'table_objet_surnoms' => array('droitsayant'), // table_objet('droits_ayant') => 'droits_ayants' 
		'field'=> array(
			"id_droits_ayant"    => "bigint(21) NOT NULL",
			"nom"                => "text NOT NULL DEFAULT ''",
			"email"              => "varchar(255) NOT NULL DEFAULT ''",
			"telephone"          => "varchar(255) NOT NULL DEFAULT ''",
			"adresse"            => "tinytext NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_droits_ayant",
		),
		'titre' => "nom AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('nom', 'email', 'telephone', 'adresse'),
		'champs_versionnes' => array('nom', 'email', 'telephone', 'adresse'),
		'rechercher_champs' => array("nom" => 10, 'email'=> 8, "adresse" => 5),
		'tables_jointures'  => array(),
	);

	$tables['spip_droits_contrats'] = array(
		'type' => 'droits_contrat',
		'principale' => "oui", 
		'table_objet_surnoms' => array('droitscontrat'), // table_objet('droits_contrat') => 'droits_contrats' 
		'field'=> array(
			"id_droits_contrat"  => "bigint(21) NOT NULL",
			"id_droits_ayant"    => "bigint(21) NOT NULL DEFAULT 0",
			"objet"              => "varchar(25) NOT NULL DEFAULT ''",
			"id_objet"           => "bigint(21) NOT NULL DEFAULT 0",
			'id_licence'         => 'smallint not null default 0',
			"date_debut"         => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"date_fin"           => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'montant'            => 'varchar(255) not null default ""',
			"credits"            => "text NOT NULL DEFAULT ''",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_droits_contrat",
			"KEY statut"         => "statut",
			"KEY id_droits_ayant"=> "id_droits_ayant",
		),
		'join' => array(
			'id_droits_contrat'  => 'id_droits_contrat',
			'id_droits_ayant'    => 'id_droits_ayant',
			'id_objet'           => 'id_objet',
			'objet'              => 'objet',
		),
		'tables_jointures' => array(
			'droits_ayants',
		),
		'titre' => "(select nom from spip_droits_ayants as da where da.id_droits_ayant=spip_droits_contrats.id_droits_ayant) AS titre, '' AS lang",
		//'date' => '',
		'champs_editables'  => array('objet', 'id_objet', 'id_droits_ayant', 'id_licence', 'date_debut', 'date_fin', 'montant', 'credits'),
		'champs_versionnes' => array('id_droits_ayant', 'id_licence', 'date_debut', 'date_fin', 'montant', 'credits'),
		'rechercher_champs' => array('credits'=>5),
		'rechercher_jointures' => array(
			'droits_ayant' => array('nom'=>8),
		),
		'statut_textes_instituer' => array(
			'a_faire'  => 'droits_contrat:statut_a_faire',
			'en_cours' => 'droits_contrat:statut_en_cours',
			'ok'       => 'droits_contrat:statut_ok',
			'refuse'   => 'droits_contrat:statut_refuse',
			'cloture'  => 'droits_contrat:statut_cloture',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'a_faire'  => 'puce-preparer-8.png',
			'en_cours' => 'puce-proposer-8.png',
			'ok'       => 'puce-publier-8.png',
			'refuse'   => 'puce-refuser-8.png',
			'cloture'  => 'puce-supprimer-8.png',
			'poubelle' => 'puce-supprimer-8.png',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'ok',
				'previsu'   => 'ok,en_cours,a_faire',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'droits_contrat:texte_changer_statut_droits_contrat', 
	);

	return $tables;
}
