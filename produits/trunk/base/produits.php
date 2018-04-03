<?php

/**
 * Déclarations relatives à la base de données
 *
 * @plugin	   produits
 * @copyright  2014
 * @author	   Les Développements Durables, http://www.ldd.fr
 * @licence	   GNU/GPL
 * @package	   SPIP\Produits\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *	   Déclarations d'interface pour le compilateur
 * @return array
 *	   Déclarations d'interface pour le compilateur
 */
function produits_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['produits'] = 'produits';

	// Champs date sur les tables
	$interface['table_date']['produits'] = 'date';

	// Déclaration du titre
	$interface['table_titre']['produits'] = 'titre, "" as lang';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *	   Description des tables
 * @return array
 *	   Description complétée des tables
 */
function produits_declarer_tables_objets_sql($tables) {

	$tables['spip_produits'] = array(
		'type' => 'produit',
		'principale' => 'oui',
		'field' => array(
			'id_produit' => 'bigint(21) NOT NULL',
			'id_rubrique' => 'bigint(21) NOT NULL DEFAULT 0',
			'id_secteur' => 'bigint(21) NOT NULL DEFAULT 0',
			'titre' => 'text NOT NULL',
			'reference' => "tinytext NOT NULL DEFAULT ''",
			'descriptif' => "text NOT NULL DEFAULT ''",
			'texte' => 'longtext NOT NULL',
			'prix_ht' => 'decimal(20,6) NOT NULL DEFAULT 0',
			'taxe' => 'decimal(4,4) DEFAULT NULL',
			'statut' => "varchar(20)  DEFAULT '0' NOT NULL",
			'lang' => "VARCHAR(10) NOT NULL DEFAULT ''",
			'date' => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'date_com' => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'langue_choisie' => "VARCHAR(3) DEFAULT 'non'",
			'id_trad' => 'bigint(21) NOT NULL DEFAULT 0',
			'immateriel' => 'tinyint(1) NOT NULL DEFAULT 0',
			'poids' => 'bigint(21) NOT NULL DEFAULT 0', // poids en g
			'largeur' => 'bigint(21) NOT NULL DEFAULT 0', // largeur en cm
			'longueur' => 'bigint(21) NOT NULL DEFAULT 0', // longueur en cm
			'hauteur' => 'bigint(21) NOT NULL DEFAULT 0', // hauteur en cm
			'maj' => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY' => 'id_produit',
			'KEY id_rubrique' => 'id_rubrique',
			'KEY id_secteur' => 'id_secteur',
			'KEY lang' => 'lang',
			'KEY id_trad' => 'id_trad',
			'KEY statut' => 'statut',
		),
		'titre' => 'titre AS titre, lang AS lang',
		'date' => 'date',
		'champs_editables' => array('titre', 'reference', 'prix_ht', 'taxe', 'descriptif', 'texte', 'immateriel', 'poids', 'largeur', 'longueur', 'hauteur'),
		'champs_versionnes' => array('titre', 'reference', 'prix_ht', 'taxe', 'descriptif', 'texte'),
		'rechercher_champs' => array('titre' => 4, 'reference' => 4, 'descriptif' => 2, 'texte' => 1),
		'statut_textes_instituer' => array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'prop' => 'texte_statut_propose_evaluation',
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut' => array(
			array(
				'champ' => 'statut',
				'publie' => 'publie',
				'previsu' => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut', 'tout')
			)
		),
		'parent' => array(
			array('type' => 'rubrique', 'champ' => 'id_rubrique'),
		),
		'texte_changer_statut' => 'produits:produit_statut',
		'join' => array(
			'id_produit' => 'id_produit',
			'id_rubrique' => 'id_rubrique'
		),
		'tables_jointures' => array(
			'profondeur' => 'rubriques',
			#'id_auteur' => 'auteurs_liens' // declaration generique plus bas
		),
	);

	return $tables;
}
