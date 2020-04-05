<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Optionsproduits produits
 * @copyright  2018
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Optionsproduits\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 *
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function optionsproduits_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['options']        = 'options';
	$interfaces['table_des_tables']['optionsgroupes'] = 'optionsgroupes';

	$interfaces['table_des_traitements']['PRIX_OPTION'][]                        = 'prix_formater(%s)';
	$interfaces['table_des_traitements']['PRIX_OPTION_HT'][]                     = 'prix_formater(%s)';
	$interfaces['table_des_traitements']['PRIX_DEFAUT']['spip_options']          = 'prix_formater(%s)';
	$interfaces['table_des_traitements']['TITRE_GROUPE']['spip_optionsgroupes']  = 'supprimer_numero(%s)';
	$interfaces['table_des_traitements']['DESCRIPTIF']['spip_commandes_details'] = _TRAITEMENT_RACCOURCIS;

	//	$interfaces['tables_jointures']['spip_optionsgroupes'][] = 'options';
	//	$interfaces['tables_jointures']['spip_options'][] = 'optionsgroupes';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 *
 * @param array $tables
 *     Description des tables
 *
 * @return array
 *     Description complétée des tables
 */
function optionsproduits_declarer_tables_objets_sql($tables) {

	$tables['spip_options'] = array(
		'type'              => 'option',
		'principale'        => 'oui',
		'field'             => array(
			'id_option'           => 'bigint(21) NOT NULL',
			'id_optionsgroupe'    => 'bigint(21) NOT NULL DEFAULT 0',
			'titre'               => 'text',
			'description'         => 'text',
			'prix_defaut'         => 'decimal(20,6)',
			'date'                => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'                 => 'TIMESTAMP',
		),
		'key'               => array(
			'PRIMARY KEY'          => 'id_option',
			'KEY id_optionsgroupe' => 'id_optionsgroupe',
		),
		'titre'             => 'titre AS titre, "" AS lang',
		'date'              => 'date',
		'champs_editables'  => array('titre', 'description', 'prix_defaut', 'id_optionsgroupe'),
		'champs_versionnes' => array('titre', 'description', 'prix_defaut', 'id_optionsgroupe'),
		'rechercher_champs' => array("titre" => 10, 'description' => 5),
		'tables_jointures'  => array('spip_options_liens'),
		'join'              => array(
			'id_option'        => 'id_option',
			'id_optionsgroupe' => 'id_optionsgroupe',
		),
		'parent'            => array('type' => 'optionsgroupe', 'champ' => 'id_optionsgroupe'),
	);

	$tables['spip_optionsgroupes'] = array(
		'type'              => 'optionsgroupe',
		'principale'        => 'oui',
		'field'             => array(
			'id_optionsgroupe' => 'bigint(21) NOT NULL',
			'titre_groupe'     => 'text',
			'date'             => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'              => 'TIMESTAMP',
		),
		'key'               => array(
			'PRIMARY KEY' => 'id_optionsgroupe',
		),
		'titre'             => 'titre_groupe AS titre, "" AS lang',
		'date'              => 'date',
		'champs_editables'  => array('titre_groupe'),
		'champs_versionnes' => array('titre_groupe'),
		'rechercher_champs' => array("titre_groupe" => 10),
		'tables_jointures'  => array('spip_options'),
		'join'              => array(
			'id_optionsgroupe' => 'id_optionsgroupe',
		),

	);

	return $tables;
}

/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 *
 * @param array $tables
 *     Description des tables
 *
 * @return array
 *     Description complétée des tables
 */
function optionsproduits_declarer_tables_auxiliaires($tables) {

	$tables['spip_options_liens'] = array(
		'field' => array(
			'id_option'         => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'          => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'             => 'varchar(25) DEFAULT "" NOT NULL',
			'prix_option_objet' => 'decimal(20,6)',
		),
		'key'   => array(
			'PRIMARY KEY'   => 'id_option,id_objet,objet',
			'KEY id_option' => 'id_option',
		),
	);

	return $tables;
}
