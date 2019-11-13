<?php
// Sécurité
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
function geographie_declarer_tables_interfaces($interface) {
	$interface['table_des_tables']['geo_continents'] = 'geo_continents';
	$interface['table_des_tables']['geo_zones'] = 'geo_zones';
	$interface['table_des_tables']['geo_pays'] = 'geo_pays';
//	$interface['table_des_tables']['geo_subdivisions'] = 'geo_subdivisions';
//	$interface['table_des_tables']['geo_communes'] = 'geo_communes';

	return $interface;
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
function geographie_declarer_tables_objets_sql($tables) {
	$tables['spip_geo_pays'] = array(
		'type' => 'geo_pays',
		'principale' => 'oui',
		'table_objet_surnoms' => array('geo_pay', 'geopay', 'geopays'), // table_objet('geopays') => 'geo_pays'
		'field' => array(
			'id_pays'       => 'bigint(21) NOT NULL',                   // Permet d'utiliser le pays comme un objet
			'code'          => 'varchar(2) default "" NOT NULL',        // Code ISO 3166 alpha2 utilisé dans les liens
			'nom'           => 'text DEFAULT "" NOT NULL',              // Nom normalisé multilingue
			'code_a3'       => "char(3) DEFAULT '' NOT NULL",           // Code ISO 3166 alpha3
			'code_num'      => "char(3) DEFAULT '' NOT NULL",           // Code ISO 3166 numérique (compatible UN M49)
			'capitale'      => "varchar(255) DEFAULT '' NOT NULL",      // Nom de la capitale
			'superficie'    => "int DEFAULT 0 NOT NULL",                // Superficie en km2
			'population'    => "int DEFAULT 0 NOT NULL",                //
			'continent'     => "char(2) DEFAULT '' NOT NULL",           // Code GeoIP du continent
			'zone'          => "char(3) DEFAULT '' NOT NULL",           // Code ISO 3166 numérique de la zone (UN M49)
			'tld'           => "char(3) DEFAULT '' NOT NULL",           // Tld - Domaine internet
			'code_devise'   => "char(3) DEFAULT '' NOT NULL",           // Code ISO-4217 de la devise du pays
			'nom_devise'    => "varchar(255) DEFAULT '' NOT NULL",      // Nom anglais de la devise
			'indicatif_uit' => "varchar(16) DEFAULT '' NOT NULL",       // Indicatif téléphonique du pays
		),
		'key' => array(
			'PRIMARY KEY'   => 'id_pays',
			'KEY code'      => 'code',
			'KEY code_a3'   => 'code',
			'KEY code_num'  => 'code'
		),
		'titre' => 'nom AS titre, "" AS lang',
		'champs_editables' => array(''),
		'champs_versionnes' => array(''),
		'rechercher_champs' => array('code' => 10, 'nom' => 8, 'code_a3' => 5),
		'tables_jointures' => array(),
	);

	$tables['spip_geo_zones'] = array(
		'type' => 'geo_zone',
		'principale' => 'oui',
		'field' => array(
			'id_zone' => 'bigint(21) NOT NULL',
			'code'    => "char(3) DEFAULT '' NOT NULL",  // Code UN M49 numérique
			'parent'  => "char(3) DEFAULT '' NOT NULL",  // Code UN M49 du parent ou ''
			'nom'     => 'text DEFAULT "" NOT NULL',     // Nom normalisé multilingue
		),
		'key' => array(
			'PRIMARY KEY' => 'id_zone',
		),
		'titre' => 'nom AS titre, "" AS lang',
		'champs_editables' => array(''),
		'champs_versionnes' => array(''),
		'rechercher_champs' => array('nom' => 10, 'code' => 8),
		'tables_jointures' => array(),
	);

	$tables['spip_geo_continents'] = array(
		'type' => 'geo_continent',
		'principale' => 'oui',
		'field' => array(
			'id_continent' => 'bigint(21) NOT NULL',
			'code'         => "char(2) DEFAULT '' NOT NULL", // Code GeoIP à deux lettres majuscules
			'nom'          => 'text DEFAULT "" NOT NULL',    // Nom normalisé multilingue
		),
		'key' => array(
			'PRIMARY KEY' => 'id_continent',
		),
		'titre' => 'nom AS titre, "" AS lang',
		'champs_editables' => array(''),
		'champs_versionnes' => array(''),
		'rechercher_champs' => array('nom' => 10, 'code' => 8),
		'tables_jointures' => array(),
	);

	// Jointures pour tous les objets
	$tables[]['tables_jointures'][] = 'geo_pays_liens';
//	$tables[]['tables_jointures'][] = 'geo_subdivisions_liens';
//	$tables[]['tables_jointures'][] = 'geo_communes_liens';

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
function geographie_declarer_tables_auxiliaires($tables) {
	$tables['spip_geo_pays_liens'] = array(
		'field' => array(
			'code' => 'VARCHAR(2) DEFAULT "" NOT NULL',
			'id_objet' => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet' => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu' => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'code,id_objet,objet',
			'KEY code' => 'code',
		),
	);
/*
	$tables['spip_geo_subdivisions_liens'] = array(
		'field' => array(
			'code' => 'VARCHAR(6) DEFAULT "" NOT NULL',
			'type_subdivision' => 'VARCHAR(32) DEFAULT "" NOT NULL',
			'id_objet' => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet' => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu' => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_region,id_objet,objet',
			'KEY id_region' => 'id_region',
		),
	);

	$tables['spip_geo_communes_liens'] = array(
		'field' => array(
			'id_commune' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet' => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet' => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu' => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_commune,id_objet,objet',
			'KEY id_commune' => 'id_commune',
		),
	);
*/
	return $tables;
}

function geographie_lister_tables_noexport($liste) {
//	$liste[] = 'spip_geo_communes';
//	$liste[] = 'spip_geo_subdivisions';
	$liste[] = 'spip_geo_continents';
	$liste[] = 'spip_geo_zones';
	$liste[] = 'spip_geo_pays';

	return $liste;
}

global $IMPORT_tables_noerase;
//$IMPORT_tables_noerase[] = 'spip_geo_communes';
//$IMPORT_tables_noerase[] = 'spip_geo_subdivisions';
$IMPORT_tables_noerase[] = 'spip_geo_continents';
$IMPORT_tables_noerase[] = 'spip_geo_zones';
$IMPORT_tables_noerase[] = 'spip_geo_pays';
