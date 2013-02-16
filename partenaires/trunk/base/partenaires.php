<?php
/**
 * Plugin Partenaires
 * (c) 2013 Teddy Payet
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function partenaires_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['partenaires'] = 'partenaires';
	$interfaces['table_des_tables']['types'] = 'types';
	
	$interfaces['exceptions_des_jointures']['partenaires']['nom_type'] = array('spip_types', 'titre');
	$interfaces['exceptions_des_jointures']['partenaires']['id_type'] = array('spip_types', 'id_type');
	/**
	 * On rajoute par sécurité et habitude spipienne le type pourvoir avoir {par type} sur une boucle PARTENAIRES
	 */
	$interfaces['exceptions_des_jointures']['partenaires']['type'] = array('spip_types', 'titre');

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
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

	$tables['spip_types'] = array(
		'type' => 'type',
		'principale' => "oui",
		'field'=> array(
			"id_type"            => "bigint(21) NOT NULL",
			"titre"              => "varchar(50) NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_type",
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre', 'descriptif'),
		'champs_versionnes' => array('descriptif'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_types_liens'),
		

	);

	// jointures sur les types pour tous les objets
	$tables[]['tables_jointures'][]= 'types_liens';
	$tables[]['tables_jointures'][]= 'types';
	
	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 */
function partenaires_declarer_tables_auxiliaires($tables) {

	$tables['spip_types_liens'] = array(
		'field' => array(
			"id_type"            => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_type,id_objet,objet",
			"KEY id_type"        => "id_type"
		)
	);

	return $tables;
}


?>