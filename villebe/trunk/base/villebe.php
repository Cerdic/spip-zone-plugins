<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Ville de belgique
 * @copyright  2015
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Villebe\Pipelines
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
function villebe_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['villes_belges'] = 'villes_belges';

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
function villebe_declarer_tables_objets_sql($tables) {

	$tables['spip_villes_belges'] = array(
		'type' => 'villes_belge',
		'principale' => "oui",
		'table_objet_surnoms' => array('villesbelge'), // table_objet('villes_belge') => 'villes_belges'
		'field'=> array(
			"id_villes_belge"    => "bigint(21) NOT NULL",
			"code_postal"        => "varchar(4) NOT NULL DEFAULT ''",
			"nom"                => "varchar(255) NOT NULL DEFAULT ''",
            "province"           => "varchar(255) NOT NULL DEFAULT ''",
		),
		'key' => array(
			"PRIMARY KEY"        => "id_villes_belge",
            "KEY"    => "code_postal"
		),
		'titre' => "nom AS titre, '' AS lang",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array()
	);

	return $tables;
}