<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Réservation Événements
 * @copyright  2013 -
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Promotions\Pipelines
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
function reservation_evenement_declarer_tables_interfaces ($interfaces) {
	$interfaces['table_des_tables']['reservations'] = 'reservations';
	$interfaces['table_des_tables']['reservations_details'] = 'reservations_details';

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
function reservation_evenement_declarer_tables_objets_sql ($tables) {
	$tables['spip_reservations'] = array(
		'type' => 'reservation',
		'principale' => "oui",
		'field' => array(
			"id_reservation" => "bigint(21) NOT NULL",
			"id_reservation_source" => "bigint(21) NOT NULL",
			"id_auteur" => "bigint(21) NOT NULL DEFAULT '0'",
			"reference" => "varchar(255) NOT NULL DEFAULT ''",
			"date_paiement" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"type_paiement" => "varchar(50) NOT NULL DEFAULT ''",
			"nom" => "varchar(255) NOT NULL DEFAULT ''",
			"email" => "varchar(255) NOT NULL DEFAULT ''",
			"type_lien" => "varchar(25) NOT NULL DEFAULT ''",
			"origine_lien" => "varchar(25) NOT NULL DEFAULT ''",
			"maj" => "timestamp",
			"donnees_auteur" => "text NOT NULL DEFAULT ''",
			"date" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut" => "varchar(20)  DEFAULT '0' NOT NULL",
			"lang" => "varchar(10)  DEFAULT '' NOT NULL",
			"langue_choisie" => "varchar(3) NOT NULL DEFAULT ''",
			"maj" => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_reservation",
			"KEY statut" => "statut,id_auteur,lang,id_reservation_source"
		),
		'titre' => "reference AS titre, '' AS lang",
		'date' => "date",
		'champs_editables' => array(
			'id_reservation_source',
			'id_auteur',
			'date_paiement',
			'nom',
			'email',
			'type_lien',
			'origine_lien',
			'donnees_auteur',
			'reference',
			'lang'
		),
		'champs_versionnes' => array(
			'id_auteur',
			'date_paiement',
			'nom',
			'email',
			'donnees_auteur',
			'reference'
		),
		'rechercher_champs' => array(
			"reference" => 8,
			"id_reservation" => 8,
			"email" => 8,
			"nom" => 8
		),
		'tables_jointures' => array(
			'spip_auteurs'
		),
		'join' => array(
			'id_auteur' => 'id_auteur'
		),
		'statut_textes_instituer' => array(
			'attente' => 'reservation:texte_statut_attente',
			'attente_paiement' => 'reservation:texte_statut_attente_paiement',
			'accepte_part' => 'reservation:texte_statut_accepte_part',
			'accepte' => 'reservation:texte_statut_accepte',
			'cloture' => 'reservation:texte_statut_cloture',
			'encours' => 'reservation:texte_statut_encours',
			'refuse' => 'reservation:texte_statut_refuse',
			'poubelle' => 'reservation:texte_statut_poubelle'
		),
		'statut_images' => array(
			'attente' => 'puce-reservation-attente-16.png',
			'attente_paiement' => 'puce-reservation-attente_paiement-16.png',
			'accepte' => 'puce-reservation-accepte-16.png',
			'accepte_part' => 'puce-reservation-accepte_part-16.png',
			'cloture' => 'puce-reservation-cloture-16.png',
			'encours' => 'puce-reservation-encours-16.png',
			'refuse' => 'puce-reservation-refuse-16.png',
			'poubelle' => 'puce-reservation-poubelle-16.png'
		),
		'statut' => array(
			array(
				'champ' => 'statut',
				'publie' => 'accepte,cloture,accepte_part',
				'previsu' => 'accepte,attente,attente_paiement,accepte_part',
				'post_date' => 'date',
				'exception' => array(
					'statut',
					'tout'
				)
			)
		),
		'texte_changer_statut' => 'reservation:texte_changer_statut_reservation'
	);

	$tables['spip_reservations_details'] = array(
		'type' => 'reservations_detail',
		'principale' => "oui",
		'table_objet_surnoms' => array(
			'reservationsdetail'
		), // table_objet('reservations_detail') => 'reservations_details'
		'field' => array(
			"id_reservations_detail" => "bigint(21) NOT NULL",
			"id_reservation" => "bigint(21) NOT NULL DEFAULT '0'",
			"id_evenement" => "bigint(21) NOT NULL DEFAULT '0'",
			"descriptif" => "text NOT NULL",
			"quantite" => "int(11) NOT NULL DEFAULT '1'",
			"prix_ht" => "decimal(15,2) NOT NULL DEFAULT '0.00'",
			"prix" => "decimal(15,2) NOT NULL DEFAULT '0.00'",
			"devise" => "varchar(3)  DEFAULT '' NOT NULL",
			"taxe" => "decimal(15,2) NOT NULL DEFAULT '0.00'",
			"statut" => "varchar(20)  DEFAULT '0' NOT NULL",
			"maj" => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_reservations_detail",
			"KEY statut" => "statut,id_reservation,id_evenement"
		),
		'titre' => "descriptif AS titre, '' AS lang",
		// 'date' => "",
		'champs_editables' => array(
			'id_reservation',
			'id_evenement',
			'descriptif',
			'quantite',
			'prix_ht',
			'prix',
			'taxe',
			'devise',
			'id_prix_objet'
		),
		'champs_versionnes' => array(
			'descriptif',
			'quantite',
			'prix_ht',
			'prix',
			'taxe',
			'devise',
			'id_prix_objet'
		),
		'rechercher_champs' => array(
			"descriptif" => 8
		),
		'tables_jointures' => array(
			'spip_evenements',
			'spip_reservations'
		),
		'join' => array(
			'id_evenement' => 'id_evenement',
			'id_reservation' => 'id_reservation'
		),
		'statut_textes_instituer' => array(
			'attente' => 'reservation:texte_statut_attente',
			'attente_paiement' => 'reservation:texte_statut_attente_paiement',
			'accepte_part' => 'reservation:texte_statut_accepte_part',
			'accepte' => 'reservation:texte_statut_accepte',
			'cloture' => 'reservation:texte_statut_cloture',
			'encours' => 'reservation:texte_statut_encours',
			'refuse' => 'reservation:texte_statut_refuse',
			'poubelle' => 'reservation:texte_statut_poubelle'
		),
		'statut_images' => array(
			'attente' => 'puce-reservation-attente-16.png',
			'attente_paiement' => 'puce-reservation-attente_paiement-16.png',
			'accepte_part' => 'puce-reservation-accepte_part-16.png',
			'accepte' => 'puce-reservation-accepte-16.png',
			'encours' => 'puce-reservation-encours-16.png',
			'cloture' => 'puce-reservation-cloture-16.png',
			'refuse' => 'puce-reservation-refuse-16.png',
			'poubelle' => 'puce-reservation-poubelle-16.png'
		),
		'statut' => array(
			array(
				'champ' => 'statut',
				'publie' => 'accepte,cloture,accepte_part',
				'previsu' => 'accepte,attente,attente_paiement,accepte_part',
				'post_date' => 'date',
				'exception' => array(
					'statut',
					'tout'
				)
			)
		),
		'texte_changer_statut' => 'reservations_detail:texte_changer_statut_reservations_detail'
	);

	// Ajouter le champ action_cloture dans le tables articles et evenements

	$tables['spip_articles']['champs_editable'][] = "action_cloture";
	$tables['spip_evenements']['champs_editable'][] = "action_cloture";

	return $tables;
}

function reservation_evenement_declarer_tables_principales ($tables_principales) {
	$tables_principales['spip_articles']['field']['action_cloture'] = "tinyint(1) NOT NULL";
	$tables_principales['spip_evenements']['field']['action_cloture'] = "tinyint(1) NOT NULL";

	return $tables_principales;
}
