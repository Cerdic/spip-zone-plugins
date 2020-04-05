<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Panolens
 * @copyright  2017
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Panolens\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function panolens_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['panoramas'] = 'panoramas';

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
function panolens_declarer_tables_objets_sql($tables) {

	$tables['spip_panoramas'] = array(
		'type' => 'panorama',
		'principale' => 'oui',
		'field'=> array(
			'id_panorama'        => 'bigint(21) NOT NULL',
			'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_panorama',
		),
		'titre' => 'titre AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('titre'),
		'champs_versionnes' => array('titre'),
		'rechercher_champs' => array("titre" => 1),
		'tables_jointures'  => array(),


	);

	return $tables;
}
