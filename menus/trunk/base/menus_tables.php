<?php

// Sécurité
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
function menus_declarer_tables_interfaces($interface) {
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['menus']='menus';
	$interface['table_des_tables']['menus_entrees']='menus_entrees';

	return $interface;
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
function menus_declarer_tables_objets_sql($tables) {

	// Menus
	$tables['spip_menus'] = array(
		'type' => 'menu',
		'principale' => 'oui',
		'field'=> array(
			'id_menu'         => 'bigint(21) NOT NULL',
			'id_menus_entree' => "bigint(21) DEFAULT '0' NOT NULL",
			'titre'           => "text DEFAULT '' NOT NULL",
			'identifiant'     => "varchar(255) default '' not null",
			'css'             => "tinytext DEFAULT '' NOT NULL"
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_menu',
			'KEY id_menu_entree' => 'id_menu_entree',
		),
		'titre' => 'titre AS titre, "" AS lang',
		// 'date' => '',
		'champs_editables'  => array('titre', 'identifiant', 'css'),
		'champs_versionnes' => array('titre', 'identifiant', 'css'),
		'rechercher_champs' => array('titre' => 4),
		'tables_jointures'  => array('spip_menus_liens'),
		// chaînes de langue
		'texte_modifier' => 'menus:icone_modifier_menu',
		'texte_creer' => 'menus:icone_creer_menu',
		'texte_creer_associer' => 'menus:texte_creer_associer_menu',
		'texte_ajouter' => 'menus:texte_ajouter_menu',
		'texte_objets' => 'menus:titre_menus',
		'texte_objet' => 'menus:titre_menu',
		'texte_logo_objet' => 'menus:titre_logo_menu',
		'info_aucun_objet' => 'menus:info_aucun_menu',
		'info_1_objet' => 'menus:info_1_menu',
		'info_nb_objets' => 'menus:info_nb_menus',
	);

	// Entrées de menus
	$tables['spip_menus'] = array(
		'type' => 'menu',
		'principale' => '',
		'field'=> array(
			'id_menus_entree' => 'bigint(21) NOT NULL',
			'id_menu'         => "bigint(21) DEFAULT '0' NOT NULL",
			'rang'            => "smallint DEFAULT '1' NOT NULL",
			'type_entree'     => "tinytext DEFAULT '' NOT NULL",
			'parametres'      => "text DEFAULT '' NOT NULL"
		),
		'key' => array(
			'PRIMARY KEY'     => 'id_menu_entree',
			'KEY id_menu'     => 'id_menu',
		),
		// 'titre' => 'titre AS titre, "" AS lang',
		// 'date' => '',
		'champs_editables'  => array('id_menu', 'rang', 'type_entree', 'parametres'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'join'              => array('id_menu' => 'id_menu'),
		'tables_jointures'  => array(),
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
function menus_declarer_tables_auxiliaires($tables) {

	$tables['spip_menus_liens'] = array(
		'field' => array(
			'id_menu'            => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_menu,id_objet,objet',
			'KEY id_menu'        => 'id_menu',
		)
	);

	return $tables;
}