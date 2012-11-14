<?php
/**
 * Plugin SpipAd - 2roues
 * (c) 2012 Collectif SPIP - Montpellier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function ad_deux_roues_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['ad_deux_roues'] = 'ad_deux_roues';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function ad_deux_roues_declarer_tables_objets_sql($tables) {

	$tables['spip_ad_deux_roues'] = array(
		'type' => 'ad_deux_roue',
		'principale' => "oui", 
		'table_objet_surnoms' => array('addeuxroue'), // table_objet('ad_deux_roue') => 'ad_deux_roues' 
		'field'=> array(
			"id_ad_deux_roue"    => "bigint(21) NOT NULL",
			"titre"              => "varchar(255) NOT NULL DEFAULT ''",
			"marque"             => "varchar(255) NOT NULL DEFAULT ''",
			"modele"             => "varchar(255) NOT NULL DEFAULT ''",
			"kilometrage"        => "BIGINT",
			"descriptif"         => "varchar(255) NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'", 
			"id_trad"            => "bigint(21) NOT NULL DEFAULT 0", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_ad_deux_roue",
			"KEY lang"           => "lang", 
			"KEY id_trad"        => "id_trad", 
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'marque', 'modele', 'kilometrage', 'descriptif'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array("titre" => 5, "modele" => 5, "descriptif" => 5),
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
		'texte_changer_statut' => 'ad_deux_roue:texte_changer_statut_ad_deux_roue', 
		

	);

	return $tables;
}



?>