<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Identifiants
 * @copyright  2015
 * @author     C.R
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Pipelines
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
function identifiants_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['identifiants'] = 'identifiants';

	return $interfaces;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function identifiants_declarer_tables_auxiliaires($tables) {

	// IDENTIFIANTS
	$tables['spip_identifiants'] = array(
		'field'=> array(
			"identifiant"    => "VARCHAR (255) DEFAULT '' NOT NULL",
			"objet"          => "VARCHAR (25) DEFAULT '' NOT NULL",
			"id_objet"       => "bigint(21) DEFAULT '0' NOT NULL",
			"maj"            => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"    => "identifiant, objet, id_objet",
		)
	);

	return $tables;
}


/**
 * Déclaration des tables des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function identifiants_declarer_tables_objets_sql($tables){

	// jointure sur spip_identifiants pour tous les objets
	$tables[]['tables_jointures'][]= 'identifiants';

	return $tables;
}


?>
