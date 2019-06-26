<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Material Icônes
 * @copyright  2019
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\Materialicons\Pipelines
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
function materialicons_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['materialicons'] = 'materialicons';

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
function materialicons_declarer_tables_objets_sql($tables) {

	$tables['spip_materialicons'] = array(
		'type' => 'materialicon',
		'principale' => 'oui',
		'field'=> array(
			'id_materialicon'    => 'bigint(21) NOT NULL',
			'style'              => 'varchar(25) NOT NULL DEFAULT ""',
			'categorie'          => 'varchar(25) NOT NULL DEFAULT ""',
			'nom'                => 'varchar(55) NOT NULL DEFAULT ""',
			'svg'                => 'text NOT NULL DEFAULT ""',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_materialicon',
		),
		'titre' => 'nom AS titre, "" AS lang',
		 #'date' => '',
		'champs_editables'  => array('style', 'categorie', 'nom', 'svg'),
		'champs_versionnes' => array('style', 'categorie', 'nom', 'svg'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_materialicons_liens'),


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
function materialicons_declarer_tables_auxiliaires($tables) {

	$tables['spip_materialicons_liens'] = array(
		'field' => array(
			'id_materialicon'    => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_materialicon,id_objet,objet',
			'KEY id_materialicon' => 'id_materialicon',
		)
	);

	return $tables;
}
