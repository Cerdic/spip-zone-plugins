<?php
/**
 * Plugin kaye
 * (c) 2012 Cédric Couvrat
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function kaye_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['classes'] = 'classes';
	$interfaces['table_des_tables']['devoirs'] = 'devoirs';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function kaye_declarer_tables_objets_sql($tables) {

	$tables['spip_classes'] = array(
		'type' => 'classe',
		'principale' => "oui",
		'field'=> array(
			"id_classe"          => "bigint(21) NOT NULL",
			"titre"              => "varchar(25) NOT NULL DEFAULT ''",
			"descriptif"         => "tinytext NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_classe",
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre', 'descriptif'),
		'champs_versionnes' => array('titre', 'descriptif'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	$tables['spip_devoirs'] = array(
		'type' => 'devoir',
		'principale' => "oui",
		'field'=> array(
			"id_devoir"          => "bigint(21) NOT NULL",
			"matiere"            => "tinytext NOT NULL DEFAULT ''",
			"id_classe"          => "int(11) NOT NULL DEFAULT 0",
			"date_echeance"      => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"texte"              => "text NOT NULL DEFAULT ''",
			"date_publication"   => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_devoir",
			"KEY statut"         => "statut", 
		),
		'titre' => "matiere AS titre, '' AS lang",
		'date' => "date_publication",
		'champs_editables'  => array('matiere', 'id_classe', 'date_echeance', 'texte'),
		'champs_versionnes' => array('matiere', 'date_echeance', 'texte'),
		'rechercher_champs' => array(),
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
		'texte_changer_statut' => 'devoir:texte_changer_statut_devoir', 

	);

	return $tables;
}



?>