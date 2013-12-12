<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Règlements
 * @copyright  2013
 * @author     Cyril MARION
 * @licence    GNU/GPL
 * @package    SPIP\Reglements\Pipelines
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
	$interfaces['table_des_traitements']['COMMENTAIRES'][] = _TRAITEMENT_RACCOURCIS;

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
			"montant"            => "decimal(18,2) DEFAULT NULL 0",
			"commentaires"       => "text NOT NULL DEFAULT ''",
			"date_reglement"     => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_reglement",
		),
		'titre' => "'' AS titre, '' AS lang",
		'date' => "date_reglement",
		'champs_editables'  => array('id_facture', 'date_reglement', 'montant', 'commentaires'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	return $tables;
}



?>