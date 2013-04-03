<?php
/**
 * Plugin ORR
 * (c) 2012 tofulm
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function orr_declarer_tables_interfaces($interfaces) {

	$interfaces['tables_jointures']['spip_orr_autorisations'][] = 'orr_autorisations_liens';
	$interfaces['tables_jointures']['spip_orr_reservations'][] = 'orr_reservations_liens';

	$interfaces['tables_jointures']['spip_orr_autorisations_liens'][] = 'orr_autorisations';
	$interfaces['tables_jointures']['spip_orr_reservations_liens'][] = 'orr_reservations';

	$interfaces['tables_jointures']['spip_orr_ressources'][] = 'orr_autorisations_liens';
	$interfaces['tables_jointures']['spip_orr_ressources'][] = 'orr_reservations_liens';

	$interfaces['tables_jointures']['spip_auteurs'][] = 'orr_autorisations_liens';
	$interfaces['tables_jointures']['spip_auteurs'][] = 'orr_reservations_liens';

	$interfaces['table_des_tables']['orr_ressources'] = 'orr_ressources';
	$interfaces['table_des_tables']['orr_autorisations'] = 'orr_autorisations';
	$interfaces['table_des_tables']['orr_autorisations_liens'] = 'orr_autorisations_liens';
	$interfaces['table_des_tables']['orr_reservations'] = 'orr_reservations';
	$interfaces['table_des_tables']['orr_reservations_liens'] = 'orr_reservations_liens';


	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function orr_declarer_tables_objets_sql($tables) {

	$tables['spip_orr_ressources'] = array(
		'type' => 'orr_ressource',
		'principale' => "oui", 
		'table_objet_surnoms' => array('orrressource'), // table_objet('orr_ressource') => 'orr_ressources' 
		'field'=> array(
			"id_orr_ressource"   => "bigint(21) NOT NULL",
			"orr_ressource_nom"  => "varchar(25) NOT NULL DEFAULT ''",
			"orr_ressource_couleur" => "varchar(25) NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_orr_ressource",
		),
		'titre' => "orr_ressource_nom AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('orr_ressource_nom','orr_ressource_couleur'),
		'champs_versionnes' => array('orr_ressource_couleur'),
		'rechercher_champs' => array("orr_ressource_nom" => 8),
		'tables_jointures'  => array(),
	);

	$tables['spip_orr_reservations'] = array(
		'type' => 'orr_reservation',
		'principale' => "oui", 
		'table_objet_surnoms' => array('orrreservation'), // table_objet('orr_reservation') => 'orr_reservations' 
		'field'=> array(
			"id_orr_reservation" => "bigint(21) NOT NULL",
			"orr_reservation_nom" => "varchar(25) NOT NULL DEFAULT ''",
			"orr_date_debut"     => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"orr_date_fin"       => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_orr_reservation",
		),
		'titre' => "orr_reservation_nom AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('orr_reservation_nom', 'orr_date_debut', 'orr_date_fin'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array("orr_reservation_nom" => 8, "orr_date_debut" => 5, "orr_date_fin" => 8),
		'tables_jointures'  => array('spip_orr_reservations_liens'),
	);
    $tables['spip_orr_autorisations'] = array(
		'type' => 'orr_autorisation',
		'principale' => "oui", 
		'table_objet_surnoms' => array('orrautorisation'), // table_objet('orr_autorisation') => 'orr_autorisations' 
		'field'=> array(
			"id_orr_autorisation"     => "bigint(21) NOT NULL",
			"orr_type_objet"          => "varchar(25) NOT NULL DEFAULT ''",
			"id_grappe"               => "bigint(21) ",
			"orr_statut"              => "varchar(25)  DEFAULT ''",
			"id_auteur"               => "bigint(21) ",
			"orr_autorisation_nom"    => "varchar(25) NOT NULL DEFAULT ''",
			"orr_autorisation_valeur" => "varchar(25) NOT NULL DEFAULT ''",
			"maj"                     => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_orr_autorisation",
		),
		 #'date' => "",
		'champs_editables'  => array('orr_type_objet','id_grappe','orr_statut'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array("orr_statut" => 8),
		'tables_jointures'  => array('spip_orr_autorisations_liens'),
	);
	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 */
function orr_declarer_tables_auxiliaires($tables) {

	$tables['spip_orr_reservations_liens'] = array(
		'field' => array(
			"id_orr_reservation" => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_orr_reservation,id_objet,objet",
			"KEY id_orr_reservation" => "id_orr_reservation"
		)
	);
    $tables['spip_orr_autorisations_liens'] = array(
		'field' => array(
			"id_orr_autorisation" => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_orr_autorisation,id_objet,objet",
			"KEY id_orr_autorisation" => "id_orr_autorisation"
		)
	);
    	return $tables;
}


?>
