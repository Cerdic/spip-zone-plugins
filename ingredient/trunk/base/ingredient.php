<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin	   ingrédients
 * @copyright  2015
 * @author	   Phenix
 * @licence	   GNU/GPL
 * @package	   SPIP\Ingredient\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) { return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *	   Déclarations d'interface pour le compilateur
 * @return array
 *	   Déclarations d'interface pour le compilateur
 */
function ingredient_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['ingredients'] = 'ingredients';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *	   Description des tables
 * @return array
 *	   Description complète des tables
 */
function ingredient_declarer_tables_objets_sql($tables) {

	$tables['spip_ingredients'] = array(
		'type' => 'ingredient',
		'principale' => 'oui',
		'field'=> array(
			'id_ingredient'		 => 'bigint(21) NOT NULL',
			'titre'				 => "text NOT NULL DEFAULT ''",
			'descriptif'		 => "text DEFAULT '' NOT NULL",
			'texte'				 => "longtext DEFAULT '' NOT NULL",
			'maj'				 => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'		 => 'id_ingredient',
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'	=> array('titre', 'descriptif', 'texte', 'unite'),
		'champs_versionnes' => array('titre', 'descriptif', 'texte', 'unite'),
		'rechercher_champs' => array('titre' => 8, 'descriptif' => 7, 'texte' => 7),
		'tables_jointures'	=> array('spip_ingredients_liens'),
	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *	   Description des tables
 * @return array
 *	   Description complétée des tables
 */
function ingredient_declarer_tables_auxiliaires($tables) {

	$tables['spip_ingredients_liens'] = array(
		'field' => array(
			'id_ingredient'		 => "bigint(21) DEFAULT '0' NOT NULL",
			'id_objet'			 => "bigint(21) DEFAULT '0' NOT NULL",
			'objet'				 => "VARCHAR(25) DEFAULT '' NOT NULL",
			'quantite'			 => "text DEFAULT '' NOT NULL",
			'vu'				 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			'PRIMARY KEY'		 => 'id_ingredient,id_objet,objet',
			'KEY id_ingredient'	 => 'id_ingredient'
		)
	);

	return $tables;
}
