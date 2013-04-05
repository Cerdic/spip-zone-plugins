<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Factures &amp; devis
 * @copyright  2013
 * @author     Cyril Marion - Ateliers CYM
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
	$interfaces['table_des_tables']['factures_lignes'] = 'factures_lignes';

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
			"num_facture"        => "varchar(50) NOT NULL",
			"id_organisation_emettrice" => "int(11) NOT NULL DEFAULT 0",
			"id_organisation"    => "int(11) DEFAULT NULL",
			"date_facture"       => "datetime DEFAULT NULL",
			"libelle_facture"    => "mediumtext",
			"montant"            => "decimal(18,2) DEFAULT NULL",
			"quantite"           => "decimal(18,2) DEFAULT NULL",
			"unite"              => "varchar(25) NOT NULL DEFAULT ''",
			"conditions"         => "text NOT NULL",
			"reglement"          => "varchar(50) DEFAULT NULL",
			"nota_bene"          => "mediumtext",
			"delais_validite"    => "int(11) DEFAULT NULL",
			"fin_validite"       => "datetime DEFAULT NULL",
			"num_devis"          => "varchar(50) DEFAULT NULL",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_facture",
		),
		'titre' => "libelle_facture AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('num_facture', 'id_organisation_emettrice', 'id_organisation', 'date_facture', 'libelle_facture', 'montant', 'quantite', 'unite', 'conditions', 'reglement', 'nota_bene', 'delais_validite', 'fin_validite', 'num_devis'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array("num_facture" => 10, "libelle_facture" => 10),
		'tables_jointures'  => array(),
		

	);

	$tables['spip_factures_lignes'] = array(
		'type' => 'factures_ligne',
		'principale' => "oui", 
		'table_objet_surnoms' => array('facturesligne'), // table_objet('factures_ligne') => 'factures_lignes' 
		'field'=> array(
			"id_factures_ligne"  => "bigint(21) NOT NULL",
			"id_facture"         => "int(11) NOT NULL DEFAULT '0'",
			"position"           => "int(2) DEFAULT NULL",
			"quantite"           => "float DEFAULT NULL",
			"unite"              => "varchar(50) DEFAULT NULL",
			"designation"        => "text",
			"prix_unitaire_ht"   => "decimal(18,2) DEFAULT NULL",
			"commentaire"        => "mediumtext",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_factures_ligne",
		),
		'titre' => "designation AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('id_facture', 'position', 'quantite', 'unite', 'designation', 'commentaire'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	return $tables;
}



?>
