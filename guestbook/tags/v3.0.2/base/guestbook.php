<?php
/**
 * Plugin Guestbook
 * (c) 2013 Yohann Prigent (potter64), Stephane Santon
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function guestbook_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['guestmessages'] = 'guestmessages';
	$interfaces['table_des_tables']['guestreponses'] = 'guestreponses';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function guestbook_declarer_tables_objets_sql($tables) {

	$tables['spip_guestmessages'] = array(
		'type' => 'guestmessage',
		'principale' => "oui",
		'field'=> array(
			"id_guestmessage"    => "bigint(21) NOT NULL",
			"guestmessage"       => "text NOT NULL DEFAULT ''",
			"email"              => "varchar(255) NOT NULL DEFAULT ''",
			"nom"                => "varchar(100) NOT NULL DEFAULT ''",
			"prenom"             => "varchar(100) NOT NULL DEFAULT ''",
			"pseudo"             => "varchar(100) NOT NULL DEFAULT ''",
			"ville"              => "varchar(100) NOT NULL DEFAULT ''",
			"ip"                 => "varchar(15) NOT NULL DEFAULT ''",
			"note"               => "int(2) ",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_guestmessage",
			"KEY statut"         => "statut", 
		),
		'titre' => "pseudo AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('guestmessage', 'email', 'nom', 'prenom', 'pseudo', 'ville', 'note', 'date'),
		'champs_versionnes' => array('guestmessage', 'note'),
		'rechercher_champs' => array("guestmessage" => 5),
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
		'texte_changer_statut' => 'guestmessage:texte_changer_statut_guestmessage', 
		

	);

	$tables['spip_guestreponses'] = array(
		'type' => 'guestreponse',
		'principale' => "oui",
		'field'=> array(
			"id_guestreponse"    => "bigint(21) NOT NULL",
			"id_guestmessage"    => "bigint(21) NOT NULL DEFAULT 0",
			"id_auteur"          => "bigint(21) NOT NULL DEFAULT 0",
			"guestreponse"       => "text NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_guestreponse",
			"KEY statut"         => "statut", 
		),
		'titre' => "guestreponse AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('id_guestmessage', 'id_auteur', 'guestreponse', 'date'),
		'champs_versionnes' => array(),
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
		'texte_changer_statut' => 'guestreponse:texte_changer_statut_guestreponse', 
		

	);

	return $tables;
}



?>