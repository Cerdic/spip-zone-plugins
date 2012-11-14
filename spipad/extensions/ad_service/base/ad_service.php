<?php
/**
 * Plugin Annonces services
 * (c) 2012 Collectif SPIP - Montpellier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function ad_service_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['ad_services'] = 'ad_services';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function ad_service_declarer_tables_objets_sql($tables) {

	$tables['spip_ad_services'] = array(
		'type' => 'ad_service',
		'principale' => "oui", 
		'table_objet_surnoms' => array('adservice'), // table_objet('ad_service') => 'ad_services' 
		'field'=> array(
			"id_ad_service"      => "bigint(21) NOT NULL",
			"titre"              => "varchar(256) NOT NULL DEFAULT ''",
			"type_service"       => "int(6) NOT NULL DEFAULT 0",
			"lattitude"          => "varchar(25) NOT NULL DEFAULT ''",
			"longitude"          => "varchar(25) NOT NULL DEFAULT ''",
			"centre"             => "bigint(21) NOT NULL DEFAULT 0",
			"rayon"              => "bigint(21) NOT NULL DEFAULT 0",
			"descriptif"         => "tinytext NOT NULL DEFAULT ''",
			"tarif_horaire"      => "varchar(25) NOT NULL DEFAULT ''",
			"deduction_fiscale"  => "varchar(3) NOT NULL DEFAULT ''",
			"cesu"               => "varchar(3) NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'", 
			"id_trad"            => "bigint(21) NOT NULL DEFAULT 0", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_ad_service",
			"KEY lang"           => "lang", 
			"KEY id_trad"        => "id_trad", 
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'type_service', 'centre', 'rayon', 'descriptif', 'tarif_horaire', 'deduction_fiscale', 'cesu'),
		'champs_versionnes' => array('titre', 'type_service', 'centre', 'rayon', 'descriptif', 'tarif_horaire', 'deduction_fiscale', 'cesu'),
		'rechercher_champs' => array("titre" => 10, "type_service" => 10, "lattitude" => 10, "longitude" => 10, "centre" => 10, "descriptif" => 10, "tarif_horaire" => 5, "deduction_fiscale" => 5, "cesu" => 10),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
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
		'texte_changer_statut' => 'ad_service:texte_changer_statut_ad_service', 
		

	);

	return $tables;
}



?>