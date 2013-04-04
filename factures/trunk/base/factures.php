<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Factures &amp; devis
 * @copyright  2013
 * @author     Cyril Marion
 * @licence    GNU/GPL
 * @package    SPIP\Factures\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function factures_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['factures'] = 'factures';
	$interfaces['table_des_tables']['lignes_factures'] = 'lignes_factures';

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
function factures_declarer_tables_objets_sql($tables) {

	$tables['spip_factures'] = array(
		'type' => 'facture',
		'principale' => "oui",
		'field'=> array(
			"id_facture"         => "bigint(21) NOT NULL",
			"id_organisation"    => "int(11) DEFAULT NULL",
			"id_type_document"   => "int(11) NOT NULL DEFAULT '1'",
			"num_facture"        => "varchar(50) NOT NULL",
			"num_devis"          => "varchar(50) DEFAULT NULL",
			"date_facture"       => "datetime DEFAULT NULL",
			"libelle_facture"    => "mediumtext",
			"conditions"         => "text NOT NULL",
			"reglement"          => "varchar(50) DEFAULT NULL",
			"delais_validite"    => "int(11) DEFAULT NULL",
			"fin_validite"       => "datetime DEFAULT NULL",
			"montant"            => "decimal(18,2) DEFAULT NULL",
			"nb_heures_vendues"  => "decimal(18,2) DEFAULT NULL",
			"nota_bene"          => "mediumtext",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_facture",
		),
		'titre' => "'' AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	$tables['spip_lignes_factures'] = array(
		'type' => 'ligne',
		'principale' => "oui", 
		'table_objet_surnoms' => array('lignesfacture'), // table_objet('ligne') => 'lignes_factures' 
		'field'=> array(
			"id_ligne"           => "bigint(21) NOT NULL",
			"id_facture"         => "int(11) NOT NULL DEFAULT '0'",
			"position"           => "int(11) DEFAULT NULL",
			"quantite"           => "float DEFAULT NULL",
			"unite"              => "varchar(50) DEFAULT NULL",
			"designation"        => "text",
			"prix_unitaire_ht"   => "decimal(18,2) DEFAULT NULL",
			"commentaire"        => "mediumtext",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_ligne",
		),
		'titre' => "'' AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	return $tables;
}



?>