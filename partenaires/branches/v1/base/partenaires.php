<?php
/**
 * Plugin Partenaires
 * (c) 2013 Teddy Payet
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function partenaires_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['partenaires'] = 'partenaires';
	$interfaces['table_des_tables']['partenaires_types'] = 'partenaires_types';

    $interfaces['exceptions_des_jointures']['partenaires']['titre_type'] = array('spip_partenaires_types', 'titre');
    $interfaces['exceptions_des_jointures']['partenaires']['id_type'] = array('spip_partenaires_types', 'id_type');

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function partenaires_declarer_tables_objets_sql($tables) {

	$tables['spip_partenaires'] = array(
		'type' => 'partenaire',
		'principale' => "oui",
		'field'=> array(
			"id_partenaire"      => "bigint(21) NOT NULL",
			"nom"                => "varchar(255) NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL",
			"url_site"           => "varchar(255) NOT NULL DEFAULT ''",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_partenaire",
			"KEY statut"         => "statut", 
		),
		'titre' => "nom AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('nom', 'descriptif', 'url_site'),
		'champs_versionnes' => array('nom', 'descriptif', 'url_site'),
		'rechercher_champs' => array("nom" => 8, "descriptif" => 6),
		'tables_jointures'  => array('spip_partenaires_liens'),
		'statut_textes_instituer' => array(
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'partenaire:texte_changer_statut_partenaire', 
		

	);

	$tables['spip_partenaires_types'] = array(
		'type' => 'partenaires_type',
		'principale' => "oui", 
		'table_objet_surnoms' => array('partenairestype'), // table_objet('partenaires_type') => 'partenaires_types' 
		'field'=> array(
			"id_type" => "bigint(21) NOT NULL",
			"titre"              => "varchar(255) NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_type",
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre', 'descriptif'),
		'champs_versionnes' => array('descriptif'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_partenaires_types_liens'),
		

	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 */
function partenaires_declarer_tables_auxiliaires($tables) {
	
	$tables['spip_partenaires_liens'] = array(
		'field' => array(
			"id_partenaire"      => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_partenaire,id_objet,objet",
			"KEY id_partenaire"  => "id_partenaire"
		)
	);

	$tables['spip_partenaires_types_liens'] = array(
		'field' => array(
			"id_type" 			 => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_type,id_objet,objet",
			"KEY id_type" 		 => "id_type"
		)
	);

	return $tables;
}


?>