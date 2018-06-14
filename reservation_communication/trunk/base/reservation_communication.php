<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Réservation Comunications
 * @copyright  2015-2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_communication\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function reservation_communication_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['reservation_communications'] = 'reservation_communications';

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
function reservation_communication_declarer_tables_objets_sql($tables) {

	$tables['spip_reservation_communications'] = array(
		'type' => 'reservation_communication',
		'principale' => "oui",
		'table_objet_surnoms' => array('reservationcommunication'), // table_objet('reservation_communication') => 'reservation_communications'
		'field' => array(
			"id_reservation_communication" => "bigint(21) NOT NULL",
			"id_rubrique" => "bigint(21) NOT NULL DEFAULT 0",
			"id_article" => "bigint(21) NOT NULL DEFAULT 0",
			"id_evenement" => "bigint(21) NOT NULL DEFAULT 0",
			"titre" => "text NOT NULL",
			"texte" => "longtext NOT NULL",
			"date_redac" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"type" => "varchar(25) NOT NULL DEFAULT ''",
			"html_email" => "longtext NOT NULL",
			"texte_email" => "longtext NOT NULL",
			"recurrence" => "text NOT NULL",
			"email_test" => "text NOT NULL",
			"total" => "bigint(21) NOT NULL DEFAULT 0",
			"current" => "bigint(21) NOT NULL DEFAULT 0",
			"failed" => "bigint(21) NOT NULL DEFAULT 0",
			"date_envoi" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut" => "varchar(20)  DEFAULT '0' NOT NULL",
			"statut_reservation" => "varchar(255)  DEFAULT '0' NOT NULL",
			"lang" => "varchar(10)  DEFAULT '' NOT NULL",
			"langue_choisie" => "varchar(10)  DEFAULT '' NOT NULL",
			"maj" => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_reservation_communication",
			"KEY id_rubrique" => "id_rubrique",
			"KEY id_evenement" => "id_evenement",
			"KEY id_article" => "id_evenement",
			"KEY statut" => "statut",
			"KEY lang" => "lang"
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date_envoi",
		'champs_editables' => array(
			'id_evenement',
			'id_article',
			'titre',
			'texte',
			'date_redac',
			'type',
			'html_email',
			'texte_email',
			'recurrence',
			'email_test',
			'total',
			'current',
			'date_envoi',
			'failed',
			'statut_reservation',
			'lang',
		),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures' => array(),
		'statut_textes_instituer' => array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'envoye' => 'reservation_communication:texte_statut_envoye',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'prepa'  => 'puce-preparer-8.png',
			'envoye'  => 'puce-publier-8.png',
			'poubelle'  => 'puce-supprimer-8.png',
		),
		'statut' => array( array(
				'champ' => 'statut',
				'publie' => 'envoye',
				'previsu' => 'envoye,prepa',
				'post_date' => 'date',
				'exception' => array(
					'statut',
					'tout'
				)
			)),
		'texte_changer_statut' => 'reservation_communication:texte_changer_statut_reservation_communication',
	);

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
function reservation_communication_declarer_tables_auxiliaires($tables) {

	$tables['spip_reservation_communication_destinataires'] = array(
		'field' => array(
			"id_reservation_communication" => "bigint(21) DEFAULT '0' NOT NULL",
			"email" => "varchar(255) NOT NULL DEFAULT ''",
			"id_auteur" => "varchar(255) NOT NULL DEFAULT ''",
			"date" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut" => "varchar(4)  DEFAULT 'todo' NOT NULL", // todo, sent, fail, [read, [clic]],[spam]
			"try" => "tinyint NOT NULL DEFAULT 0", // nombre d'essais
		),
		'key' => array(
			"PRIMARY KEY" => "id_reservation_communication,email",
			"KEY email" => "email",
			"KEY id_auteur" => "id_auteur",
			"KEY statut" => "statut"
		)
	);

	return $tables;
}
