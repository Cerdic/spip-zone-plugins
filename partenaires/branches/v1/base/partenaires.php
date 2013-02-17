<?php
/**
 * Plugin Partenaires
 * (c) 2013 Teddy Payet
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * D残laration des alias de tables et filtres automatiques de champs
 */
function partenaires_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['partenaires'] = 'partenaires';
	$interfaces['table_des_tables']['options'] = 'options';
	
	$interfaces['exceptions_des_jointures']['partenaires']['nom_option'] = array('spip_options', 'titre');
	$interfaces['exceptions_des_jointures']['partenaires']['id_option'] = array('spip_options', 'id_option');

	return $interfaces;
}


/**
 * D残laration des objets 仕itoriaux
 */
function partenaires_declarer_tables_objets_sql($tables) {

	$tables['spip_partenaires'] = array(
		'type' => 'partenaire',
		'principale' => "oui",
		'field'=> array(
			"id_partenaire"      => "bigint(21) NOT NULL",
			"nom"                => "varchar(75) NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"url_site"           => "varchar(255) NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_partenaire",
		),
		'titre' => "nom AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('nom', 'descriptif', 'url_site'),
		'champs_versionnes' => array('descriptif'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	$tables['spip_options'] = array(
		'type' => 'option',
		'principale' => "oui",
		'field'=> array(
			"id_option"          => "bigint(21) NOT NULL",
			"titre"              => "varchar(75) NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_option",
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre', 'descriptif'),
		'champs_versionnes' => array('descriptif'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_options_liens'),
		

	);

	// jointures sur les options de partenariat pour tous les objets
	$tables[]['tables_jointures'][]= 'options_liens';
	$tables[]['tables_jointures'][]= 'options';
	return $tables;
}


/**
 * D残laration des tables secondaires (liaisons)
 */
function partenaires_declarer_tables_auxiliaires($tables) {

	$tables['spip_options_liens'] = array(
		'field' => array(
			"id_option"          => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_option,id_objet,objet",
			"KEY id_option"      => "id_option"
		)
	);

	return $tables;
}


?>