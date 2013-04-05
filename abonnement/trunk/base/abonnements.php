<?php
/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function abonnements_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['abonnements_offres'] = 'abonnements_offres';
	$interfaces['table_des_tables']['abonnements'] = 'abonnements';
	$interfaces['table_des_tables']['abonnements_offres_notifications'] = 'abonnements_offres_notifications';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function abonnements_declarer_tables_objets_sql($tables) {
	$tables['spip_abonnements_offres'] = array(
		'type' => 'abonnements_offre',
		'principale' => "oui", 
		'table_objet_surnoms' => array('abonnementsoffre'), // table_objet('abonnement') => 'abonnements_offres' 
		'field'=> array(
			"id_abonnements_offre" => "bigint(21) NOT NULL",
			"titre"                => "text NOT NULL DEFAULT ''",
			"descriptif"           => "text NOT NULL DEFAULT ''",
			"duree"                => "int(11) NOT NULL DEFAULT 0",
			"periode"              => "varchar(25) NOT NULL DEFAULT ''",
			'prix'                 => 'float(10,2) not null default 0',
			"statut"               => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                  => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_abonnements_offre",
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre', 'descriptif', 'duree', 'periode', 'prix'),
		'champs_versionnes' => array('titre', 'descriptif', 'duree', 'periode', 'prix'),
		'rechercher_champs' => array("titre" => 10, "descriptif" => 5),
		'tables_jointures'  => array('spip_abonnements_offres_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'publie'   => 'texte_statut_publie',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prepa',
				'exception' => array('statut','tout')
			)
		),
		'texte_modifier' => 'abonnementsoffre:icone_modifier_abonnementsoffre',
		'texte_creer' => 'abonnementsoffre:icone_creer_abonnementsoffre',
		'texte_creer_associer' => 'abonnementsoffre:texte_creer_associer_abonnementsoffre',
		'texte_ajouter' => 'abonnementsoffre:texte_ajouter_abonnementsoffre',
		'texte_objets' => 'abonnementsoffre:titre_abonnementsoffres',
		'texte_objet' => 'abonnementsoffre:titre_abonnementsoffre',
		'texte_logo_objet' => 'abonnementsoffre:titre_logo_abonnementsoffre',
		'texte_langue_objet' => 'abonnementsoffre:titre_langue_abonnementsoffre',
		'info_aucun_objet' => 'abonnementsoffre:info_aucun_abonnementsoffre',
		'info_1_objet' => 'abonnementsoffre:info_1_abonnementsoffre',
		'info_nb_objets' => 'abonnementsoffre:info_nb_abonnementsoffres',
		'texte_changer_statut' => 'abonnementsoffre:texte_changer_statut_abonnementsoffre',
	);

	$tables['spip_abonnements'] = array(
		'type' => 'abonnement',
		'principale' => "oui",
#		'url_voir' => false,
#		'url_edit' => false,
		'field'=> array(
			"id_abonnement"      => "bigint(21) NOT NULL",
			"id_abonnements_offre" => "bigint(21) NOT NULL DEFAULT 0",
			"id_auteur"          => "bigint(21) NOT NULL DEFAULT 0",
			"date_debut"         => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"date_fin"           => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_abonnement",
			"KEY statut"         => "statut",
			"KEY id_abonnements_offre" => "id_abonnements_offre",
			"KEY id_auteur" => "id_auteur",
		),
		'titre' => "'' AS titre, '' AS lang",
		'date' => "date_debut",
		'champs_editables'  => array('id_abonnements_offre', 'id_auteur', 'date_debut', 'date_fin'),
		'champs_versionnes' => array('id_abonnements_offre', 'id_auteur', 'date_debut', 'date_fin'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'actif'    => 'abonnement:statut_actif',
			'inactif' => 'abonnement:statut_inactif',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'prepa' => 'puce-preparer-8.png',
			'actif' => 'puce-publier-8.png',
			'inactif' => 'puce-refuser-8.png',
			'poubelle' => 'puce-supprimer-8.png',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'actif',
				'previsu'   => 'actif,inactif',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'abonnement:texte_changer_statut_abonnement', 
	);

	return $tables;
}

/*
 * Déclaration des tables principales non objet
 */
function abonnements_declarer_tables_principales($tables) {
	$tables['spip_abonnements_offres_notifications'] = array(
		'field' => array(
			'id_abonnements_offres_notification' => 'bigint(21) not null',
			'id_abonnements_offre'	=> 'bigint(21) DEFAULT 0 NOT NULL',
			'duree'					=> 'int(11) NOT NULL DEFAULT 0',
			'periode'				=> 'varchar(25) NOT NULL DEFAULT ""',
		),
		'key' => array(
			'PRIMARY KEY'			=> 'id_abonnements_offres_notification',
			'KEY id_abonnements_offre' => 'id_abonnements_offre',
		),
	);
	
	return $tables;
}

/**
 * Déclaration des tables secondaires (liaisons)
 */
function abonnements_declarer_tables_auxiliaires($tables) {
	$tables['spip_abonnements_offres_liens'] = array(
		'field' => array(
			"id_abonnements_offre" => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"             => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"                => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                   => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_abonnements_offre,id_objet,objet",
			"KEY id_abonnements_offre"  => "id_abonnements_offre"
		)
	);
	
	return $tables;
}

?>
