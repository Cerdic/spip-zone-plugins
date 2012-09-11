<?php
/**
 * Déclarations relatives à la base de données
 * 
 * @package SPIP\Elements\Pipelines
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclarer les interfaces des tables du plugin éléments pour le compilateur
 * 
 * @pipeline declarer_tables_interfaces
 * 
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
**/
function elements_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['elements'] = 'elements';
	$interfaces['table_des_traitements']['ELEMENTS']['elements'] = "@unserialize(%s)";
	return $interfaces;
}


/**
 * Déclarer les tables principales du plugin elements
 *
 * @pipeline declarer_tables_principales
 * @param array $tables_principales
 *     Description des tables
 * @return array
 *     Description complétée des tables
**/
function elements_declarer_tables_principales($tables_principales){

	// Table formulaires_reponses_champs 
	$elements_champs = array(
		"id_element" => "bigint(21) NOT NULL DEFAULT 0",
		"id_objet"   => "bigint(21) DEFAULT 0 NOT NULL",
		"objet"      => "varchar(25) DEFAULT '' NOT NULL",
		"bloc"       => "varchar(25) DEFAULT '' NOT NULL",
		"elements"   => "text NOT NULL DEFAULT ''",
		"maj" => "timestamp"
	);
	$elements_cles = array(
		"PRIMARY KEY" => "id_element",
		"KEY id_objet" => "id_objet",
		"KEY objet" => "objet"
	);
	$tables_principales['spip_elements'] = array(
		'field' => &$elements_champs,
		'key' => &$elements_cles
	);

	return $tables_principales;
}
