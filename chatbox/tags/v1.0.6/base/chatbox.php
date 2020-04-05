<?php
/**
 * Plugin Chatbox
 * (c) 2013 g0uZ
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function chatbox_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['chatbox_messages'] = 'chatbox_messages';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function chatbox_declarer_tables_objets_sql($tables) {

	$tables['spip_chatbox_messages'] = array(
		'type' => 'chatbox_message',
		'principale' => "oui", 
		'table_objet_surnoms' => array('chatboxmessage'), // table_objet('chatbox_message') => 'chatbox_messages' 
		'field'=> array(
			"id_chatbox_message" => "bigint(21) NOT NULL",
			"id_auteur"          => "int(11) DEFAULT NULL",
			"message"            => "text NOT NULL",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_chatbox_message",
			"KEY lang"           => "lang", 
			"KEY statut"         => "statut", 
		),
		'titre' => "message AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('message'),
		'champs_versionnes' => array('message'),
		'rechercher_champs' => array("message" => 1),
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
		'texte_changer_statut' => 'chatbox_message:texte_changer_statut_chatbox_message', 
		

	);

	return $tables;
}



?>