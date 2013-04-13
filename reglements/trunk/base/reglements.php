<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Règlements
 * @copyright  2013
 * @author     Cyril MARION
 * @licence    GNU/GPL
 * @package    SPIP\Reglements_factures\Pipelines
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
function reglements_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['reglements'] = 'reglements';

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
function reglements_declarer_tables_objets_sql($tables) {

	$tables['spip_reglements'] = array(
		'type' => 'reglement',
		'principale' => "oui",
		'field'=> array(
			"id_reglement"       => "bigint(21) NOT NULL",
			"id_facture"         => "int(11) NOT NULL DEFAULT '0'",
			"date_reglement"     => "datetime DEFAULT NULL",
			"montant"            => "decimal(18,2) DEFAULT NULL",
			"commentaires"       => "text",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_reglement",
		),
		'titre' => "date_reglement AS titre, '' AS lang",
		//'titre' => "CONCAT(montant,' ',date_reglement,' ',id_facture) AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	return $tables;
}



?>