<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     SVG en base de données
 * @copyright  2019
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\Svgbase\Pipelines
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
function svgbase_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['svg'] = 'svg';

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
function svgbase_declarer_tables_objets_sql($tables) {

	$tables['spip_svg'] = array(
		'type' => 'svg',
		'principale' => 'oui',
		'table_objet_surnoms' => array('svg'), // table_objet('svg') => 'svg' 
		'field'=> array(
			'id_svg'             => 'bigint(21) NOT NULL',
			'titre'              => 'varchar(55) NOT NULL DEFAULT ""',
			'descriptif'         => 'text NOT NULL DEFAULT ""',
			'svg'                => 'text NOT NULL DEFAULT ""',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_svg',
		),
		'titre' => 'titre AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('titre', 'descriptif', 'svg'),
		'champs_versionnes' => array('titre', 'descriptif', 'svg'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_svg_liens'),


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
function svgbase_declarer_tables_auxiliaires($tables) {

	$tables['spip_svg_liens'] = array(
		'field' => array(
			'id_svg'             => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_svg,id_objet,objet',
			'KEY id_svg'         => 'id_svg',
		)
	);

	return $tables;
}
