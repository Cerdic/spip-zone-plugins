<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Réseŕvations Crédits
 * @copyright  2015-20
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_credits\Pipelines
 */
if (! defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interfaces
 *        	Déclarations d'interface pour le compilateur
 * @return array Déclarations d'interface pour le compilateur
 */
function reservations_credits_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['reservation_credit_mouvements'] = 'reservation_credit_mouvements';
	$interfaces['table_des_tables']['reservation_credits'] = 'reservation_credits';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 *
 * @param array $tables
 *        	Description des tables
 * @return array Description complétée des tables
 */
function reservations_credits_declarer_tables_objets_sql($tables) {
	$tables['spip_reservation_credit_mouvements'] = array (
		'type' => 'reservation_credit_mouvement',
		'principale' => "oui",
		'table_objet_surnoms' => array (
			'reservationcreditmouvement'
		), // table_objet('reservation_credit_mouvement') => 'reservation_credit_mouvements'
		'field' => array (
			"id_reservation_credit_mouvement" => "bigint(21) NOT NULL",
			"id_reservation_credit" => "int(11) NOT NULL DEFAULT 0",
			"id_reservation" => "int(11) NOT NULL DEFAULT 0",
			"id_reservations_detail" => "int(11) NOT NULL DEFAULT 0",
			"id_objet" => "int(11) NOT NULL DEFAULT 0",
			"objet" => "varchar(50) NOT NULL DEFAULT ''",
			"descriptif" => "text NOT NULL DEFAULT ''",
			"type" => "varchar(6) NOT NULL DEFAULT ''",
			"montant" => "float NOT NULL DEFAULT '0'",
			"devise" => "varchar(3) NOT NULL DEFAULT ''",
			"date_creation" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"maj" => "TIMESTAMP"
		),
		'key' => array (
			"PRIMARY KEY" => "id_reservation_credit_mouvement",
			"KEY id_reservation_credit" => "id_reservation_credit",
			"KEY id_reservations_detail" => "id_reservations_detail",
			"KEY id_reservation" => "id_reservation",
			"KEY id_objet" => "id_objet",
			"KEY objet" => "objet" ,
			"KEY type" => "type",
		),
		'titre' => "descriptif AS titre, '' AS lang",
		'date' => "date_creation",
		'champs_editables' => array (
			'id_reservation_credit',
			'id_reservation',
			'id_reservations_detail',
			'id_objet',
			'objet',
			'descriptif',
			'type',
			'montant',
			'date_creation',
			'devise'
		),
		'champs_versionnes' => array (
			'id_reservation',
			'id_reservations_detail',
			'reservation_credit',
			'id_objet',
			'objet',
			'descriptif',
			'type',
			'montant',
			'date_creation',
			'devise'
		),
		'rechercher_champs' => array (
			"type" => 1,
			"montant" => 8
		),
		'tables_jointures' => array (
			'id_reservation_credit',
			'id_reservation',
			'id_reservations_detail'
		)
	);

	$tables['spip_reservation_credits'] = array (
		'type' => 'reservation_credit',
		'principale' => "oui",
		'table_objet_surnoms' => array (
			'reservationcredit'
		), // table_objet('reservation_credit') => 'reservation_credits'
		'field' => array (
			"id_reservation_credit" => "bigint(21) NOT NULL",
			"email" => "varchar(255) NOT NULL DEFAULT ''",
			"credit" => "varchar(255) NOT NULL DEFAULT ''",
			"date_creation" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"maj" => "TIMESTAMP"
		),
		'key' => array (
			"PRIMARY KEY" => "id_reservation_credit",
			"KEY email" => "email"
		),
		'titre' => "email AS titre, '' AS lang",
		'date' => "date_creation",
		'champs_editables' => array (
			'email',
			'credit'
		),
		'champs_versionnes' => array (
			'email',
			'credit'
		),
		'rechercher_champs' => array (
			"email" => 2
		),
		'tables_jointures' => array ()
	);

	// Ajouter le statut annulé aux événements
	$tables['spip_evenements']['statut_textes_instituer']['annule'] = 'reservation_credit:texte_statut_annule';
	$tables['spip_evenements']['statut_images'] = array (
		'prop' => 'puce-proposer-8.png',
		'publie' => 'puce-publier-8.png',
		'annule' => 'puce-refuser-8.png',
		'poubelle' => 'puce-supprimer-8.png'
	);

	return $tables;
}
