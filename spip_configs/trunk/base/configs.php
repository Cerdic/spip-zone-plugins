<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     SPIP Variables
 * @copyright  2017
 * @author     tofulm
 * @licence    GNU/GPL
 * @package    SPIP\Configs\Pipelines
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
function configs_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['configs'] = 'configs';

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
function configs_declarer_tables_objets_sql($tables) {

	$tables['spip_configs'] = array(
		'type'       => 'config',
		'principale' => 'oui',
		'field'=> array(
			'id_config'          => 'bigint(21) NOT NULL',
			'nom'                => 'tinytext NOT NULL DEFAULT ""',
			'commentaire'        => 'tinytext NOT NULL DEFAULT ""',
			'prefixe'            => 'tinytext NOT NULL DEFAULT ""',
			'nom_valeur'         => 'tinytext NOT NULL DEFAULT ""',
			'defaut'             => "text DEFAULT '' NOT NULL",
			'valeur'             => "text DEFAULT '' NOT NULL",
			'rang'               => 'bigint(21) NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_config',
		),
		'rechercher_champs' => array("nom_valeur" => 8),
	);

	return $tables;
}
