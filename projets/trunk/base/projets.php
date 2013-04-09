<?php
/**
 * Plugin projets
 * (c) 2012 Cyril Marion
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function projets_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['projets'] = 'projets';
	$interfaces['table_des_tables']['projets_cadres'] = 'projets_cadres';

	$interfaces['table_des_traitements']['OBJECTIF']['projets'] = _TRAITEMENT_RACCOURCIS;
	$interfaces['table_des_traitements']['ENJEUX']['projets']   = _TRAITEMENT_RACCOURCIS;
	$interfaces['table_des_traitements']['METHODE']['projets']  = _TRAITEMENT_RACCOURCIS;

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function projets_declarer_tables_objets_sql($tables) {

	$tables['spip_projets'] = array(
		'type' => 'projet',
		'principale' => "oui",
		'field'=> array(
			"id_projet"             => "bigint(21) NOT NULL",
			"id_parent"             => "int(11) DEFAULT 0 NOT NULL",
			"nom"                   => "varchar(75) DEFAULT '' NOT NULL",
			"url_site"              => "varchar(255) DEFAULT '' NOT NULL",
			"id_projets_cadre"      => "int(11) DEFAULT 0 NOT NULL",
			"date_debut"            => "datetime NULL DEFAULT NULL",
			"date_livraison_prevue" => "datetime NULL DEFAULT NULL",
			"date_livraison"        => "datetime NULL DEFAULT NULL",
			"nb_heures_estimees"    => "decimal(18,2) DEFAULT NULL",
			"nb_heures_reelles"     => "decimal(18,2) DEFAULT NULL",
			"actif"                 => "varchar(3) NOT NULL DEFAULT 'oui'",
			"objectif"              => "text NOT NULL DEFAULT ''",
			"enjeux"                => "text NOT NULL DEFAULT ''",
			"methode"               => "text NOT NULL DEFAULT ''",
			"descriptif"            => "text NOT NULL DEFAULT ''",
			"date_publication"      => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"                => "varchar(20)  DEFAULT 'gestation' NOT NULL",
			"maj"                   => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_projet",
			"KEY statut"         => "statut",
		),
		'titre' => "nom AS titre, '' AS lang",
		'date' => "date_publication",
		'champs_editables'  => array('id_parent', 'nom', 'url_site', 'id_projets_cadre', 'date_debut', 'date_livraison_prevue', 'date_livraison', 'nb_heures_estimees', 'nb_heures_reelles', 'actif', 'objectif', 'enjeux', 'methode', 'descriptif'),
		'champs_versionnes' => array('nom', 'objectif', 'enjeux', 'methode', 'descriptif'),
		'rechercher_champs' => array("nom" => 7, "url_site" => 2, "objectif" => 3, "enjeux" => 2, "methode" => 1, "descriptif" => 2),
		'tables_jointures'  => array('spip_projets_liens'),
		'statut_textes_instituer' => array(
			'prepa'          => 'projet:texte_statut_preparation',
			'redaction'      => 'projet:texte_statut_redaction',
			'elabore'        => 'projet:texte_statut_elabore',
			'chiffrage'      => 'projet:texte_statut_chiffrage',
			'propose'        => 'projet:texte_statut_propose',
			'accepte'        => 'projet:texte_statut_accepte',
			'accord'         => 'projet:texte_statut_accord',
			'fabrication'    => 'projet:texte_statut_fabrication',
			'fabrique'       => 'projet:texte_statut_fabrique',
			'test'           => 'projet:texte_statut_test',
			'recette'        => 'projet:texte_statut_recette',
			'production'     => 'projet:texte_statut_production',
			'cloture'        => 'projet:texte_statut_cloture',
			'arrete'         => 'projet:texte_statut_arrete',
			'abandonne'      => 'projet:texte_statut_abandonne',
			'poubelle'       => 'projet:texte_statut_poubelle',
		),

		'statut_images' => array(
			'prepa'          => 'puce-think-start-8.png',
			'redaction'      => 'puce-think-half-8.png',
			'elabore'        => 'puce-think-full-8.png',
			'chiffrage'      => 'puce-quote-start-8.png',
			'propose'        => 'puce-quote-half-8.png',
			'accepte'        => 'puce-quote-full-8.png',
			'accord'         => 'puce-zebra-start-8.png',
			'fabrication'    => 'puce-zebra-half-8.png',
			'fabrique'       => 'puce-zebra-full-8.png',
			'test'           => 'puce-prod-start-8.png',
			'recette'        => 'puce-prod-half-8.png',
			'production'     => 'puce-prod-full-8.png',
			'cloture'        => 'puce-prod-stop-8.png',
			'arrete'         => 'puce-zebra-stop-8.png',
			'abandonne'      => 'puce-think-stop-8.png',
			'poubelle'       => 'puce-supprimer-8.png',
		),

		'statut'=> array(
			array(
				'champ'      => 'statut',
				'publie'     => 'redaction,elabore,chiffrage,propose,accepte,accord,fabrication,fabrique,recette,production',
				'previsu'    => '!poubelle',
				'post_date'  => 'date',
				'exception'  => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'projet:texte_changer_statut_projet',

	);

	$tables['spip_projets_cadres'] = array(
		'type' => 'projets_cadre',
		'principale' => "oui",
		'table_objet_surnoms' => array('projetscadre'), // table_objet('projets_cadre') => 'projets_cadres'
		'field'=> array(
			"id_projets_cadre"   => "bigint(21) NOT NULL",
			"titre"              => "tinytext NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_projets_cadre",
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre', 'descriptif'),
		'champs_versionnes' => array('titre', 'descriptif'),
		'rechercher_champs' => array("titre" => 2),
		'tables_jointures'  => array(),

	);


	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 */
function projets_declarer_tables_auxiliaires($tables) {

	$tables['spip_projets_liens'] = array(
		'field' => array(
			"id_projet"          => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_projet,id_objet,objet",
			"KEY id_projet"      => "id_projet"
		)
	);

	return $tables;
}


?>
