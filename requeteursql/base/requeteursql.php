<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Requêteur SQL
 * @copyright  2014
 * @author     David Dorchies
 * @licence    GNU/GPL
 * @package    SPIP\Requeteursql\Pipelines
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
function requeteursql_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['sql_requetes'] = 'sql_requetes';

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
function requeteursql_declarer_tables_objets_sql($tables) {

	$tables['spip_sql_requetes'] = array(
		'type' => 'sql_requete',
		'principale' => "oui", 
		'table_objet_surnoms' => array('sqlrequete'), // table_objet('sql_requete') => 'sql_requetes' 
		'field'=> array(
			"id_sql_requete"     => "bigint(21) NOT NULL",
			"titre"              => "varchar(250) NOT NULL DEFAULT ''",
			"description"        => "text NOT NULL DEFAULT ''",
			"requetesql"         => "text NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_sql_requete",
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre', 'description', 'requetesql'),
		'champs_versionnes' => array('titre', 'description', 'requetesql'),
		'rechercher_champs' => array("titre" => 5, "description" => 4, "requetesql" => 1),
		'tables_jointures'  => array(),
		

	);

	return $tables;
}



?>