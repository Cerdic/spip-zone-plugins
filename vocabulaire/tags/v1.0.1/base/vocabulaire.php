<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Dictionnaire français
 * @copyright  2016
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\vocabulaire\Pipelines
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
function vocabulaire_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['dict_fr'] = 'dict_fr';

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
function vocabulaire_declarer_tables_objets_sql($tables) {

	$tables['spip_vocabulaires'] = array(
		'type' => 'vocabulaire',
		'principale' => 'oui',
		'table_objet_surnoms' => array('vocabulaire'), // table_objet('dict_fr') => 'dict_fr'
		'field'=> array(
			'id_dict_fr'         => 'bigint(21) NOT NULL',
			'mot'                => 'varchar(255) NOT NULL DEFAULT ""'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_dict_fr',
			'KEY mot'            => 'mot'
		),
		'titre' => 'mot AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('mot'),
		'champs_versionnes' => array('mot'),
		'tables_jointures'  => array(),
	);

	return $tables;
}
