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
	$interfaces['table_des_tables']['villes_belges_liens'] = 'villes_belges_liens';

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
		'principale' => 'oui',
		'table_objet_surnoms' => array('villesbelge'), // table_objet('villes_belge') => 'villes_belges'
		'field'=> array(
			'id_villes_belge'    => 'bigint(21) NOT NULL',
			'code_postal'        => "varchar(4) NOT NULL DEFAULT ''",
			'nom'                => "varchar(255) NOT NULL DEFAULT ''",
			'province'           => "varchar(255) NOT NULL DEFAULT ''",
			'lat' => 'double NULL NULL',
			'lon' => 'double NULL NULL',
			'entite'    => "varchar(3) NOT NULL DEFAULT 'non'"
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_villes_belge',
            'KEY code_postal'    => 'code_postal',
			'KEY entite'    => 'entite'
		),
		'titre' => "nom AS titre, '' AS lang",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_villes_belges_liens')
	);

	return $tables;
}

function villebe_declarer_tables_auxiliaires($tables_auxiliaires) {

	$tables_auxiliaires['spip_villes_belges_liens'] = array(
		'field' => array(
			'id_villes_belge' => 'bigint(21) NOT NULL',
			'objet' => "VARCHAR (25) DEFAULT '' NOT NULL",
			'id_objet' => 'bigint(21) NOT NULL'
		),
		'key' => array(
			'PRIMARY KEY' => 'id_villes_belge,id_objet,objet',
			'KEY id_villes_belge' => 'id_villes_belge',
			'KEY id_objet' => 'id_objet',
			'KEY objet' => 'objet'
		)
	);

	return $tables_auxiliaires;
}
