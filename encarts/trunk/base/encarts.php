<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     encarts
 * @copyright  2013
 * @author     Cyril
 * @licence    GNU/GPL
 * @package    SPIP\Encarts\Pipelines
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
function encarts_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['encarts'] = 'encarts';

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
function encarts_declarer_tables_objets_sql($tables) {

	$tables['spip_encarts'] = array(
		'type' => 'encart',
		'principale' => "oui",
		'field'=> array(
			"id_encart"          => "bigint(21) NOT NULL",
			"titre"              => "tinytext NOT NULL",
			"texte"              => "text NOT NULL",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_encart",
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_encarts_liens'),
		

	);

	return $tables;
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
function encarts_declarer_tables_auxiliaires($tables) {

	$tables['spip_encarts_liens'] = array(
		'field' => array(
			"id_encart"          => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_encart,id_objet,objet",
			"KEY id_encart"      => "id_encart"
		)
	);

	return $tables;
}


?>