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
	$interface['table_des_tables']['geo_pays'] = 'geo_pays';
	$interface['table_des_tables']['geo_regions'] = 'geo_regions';
	$interface['table_des_tables']['geo_departements'] = 'geo_departements';
	$interface['table_des_tables']['geo_arrondissements'] = 'geo_arrondissements';
	$interface['table_des_tables']['geo_communes'] = 'geo_communes';

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
			'id_pays' => 'smallint NOT NULL',
			'code' => 'varchar(2) default "" NOT NULL',
			'nom' => 'text DEFAULT "" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_pays',
		),
		'titre' => 'nom AS titre, "" AS lang',
		'champs_editables' => array(''),
		'champs_versionnes' => array(''),
		'rechercher_champs' => array('code' => 10, 'nom' => 8),
		'tables_jointures' => array(),
	);

	$tables['spip_geo_regions'] = array(
		'type' => 'geo_region',
		'principale' => 'oui',
		'table_objet_surnoms' => array('georegion'),
		'field' => array(
			'id_region' => 'smallint NOT NULL',
			'id_pays' => 'smallint NOT NULL',
			'nom' => 'tinytext DEFAULT "" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_region',
		),
		'titre' => 'nom AS titre, "" AS lang',
		'champs_editables' => array(''),
		'champs_versionnes' => array(''),
		'rechercher_champs' => array('nom' => 10),
		'tables_jointures' => array(),
	);

	$tables['spip_geo_departements'] = array(
		'type' => 'geo_departement',
		'principale' => 'oui',
		'table_objet_surnoms' => array('geodepartement'),
		'field' => array(
			'id_departement' => 'smallint NOT NULL',
			'abbr' => 'varchar(5) default "" NOT NULL',
			'id_region' => 'smallint NOT NULL',
			'nom' => 'tinytext DEFAULT "" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_departement',
		),
		'titre' => 'nom AS titre, "" AS lang',
		'champs_editables' => array(''),
		'champs_versionnes' => array(''),
		'rechercher_champs' => array('nom' => 10, 'abbr' => 5),
		'tables_jointures' => array(),
	);

	$tables['spip_geo_arrondissements'] = array(
		'type' => 'geo_arrondissement',
		'principale' => 'oui',
		'table_objet_surnoms' => array('geoarrondissement'),
		'field' => array(
			'id_arrondissement' => 'bigint(21) NOT NULL',
			'id_departement' => 'smallint NOT NULL',
			'nom' => 'tinytext DEFAULT "" NOT NULL',
			'id_commune' => 'bigint(21) NOT NULL',
			'population' => 'integer DEFAULT 0',
			'superficie' => 'integer DEFAULT 0',
			'densite' => 'integer DEFAULT 0',
			'nb_cantons' => 'integer DEFAULT 0',
			'nb_communes' => 'integer DEFAULT 0',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_arrondissement',
		),
		'titre' => 'nom AS titre, "" AS lang',
		'champs_editables' => array(''),
		'champs_versionnes' => array(''),
		'rechercher_champs' => array('nom' => 10),
		'tables_jointures' => array(),
	);

	$tables['spip_geo_communes'] = array(
		'type' => 'geo_commune',
		'principale' => 'oui',
		'table_objet_surnoms' => array('geocommune'),
		'field' => array(
			'id_commune' => 'bigint(21) NOT NULL',
			'insee' => 'char(6) default "" NOT NULL',
			'id_departement' => 'smallint NOT NULL',
			'id_pays' => 'smallint NOT NULL',
			'code_postal' => 'char(5) default "" NOT NULL',
			'nom' => 'tinytext DEFAULT "" NOT NULL',
			'longitude' => 'varchar(15) default "" NOT NULL',
			'latitude' => 'varchar(15) default "" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_commune',
			'INDEX insee' => 'insee',
			'INDEX id_pays' => 'id_pays',
		),
		'titre' => 'nom AS titre, "" AS lang',
		'champs_editables' => array(''),
		'champs_versionnes' => array(''),
		'rechercher_champs' => array('nom' => 10, 'code_postal' => 5),
		'tables_jointures' => array(),
	);

	// Jointures pour tous les objets
	$tables[]['tables_jointures'][] = 'geo_pays_liens';
	$tables[]['tables_jointures'][] = 'geo_regions_liens';
	$tables[]['tables_jointures'][] = 'geo_departements_liens';
	$tables[]['tables_jointures'][] = 'geo_arrondissements_liens';
	$tables[]['tables_jointures'][] = 'geo_communes_liens';

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
			'id_pays' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet' => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet' => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu' => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_pays,id_objet,objet',
			'KEY id_pays' => 'id_pays',
		),
	);

	$tables['spip_geo_regions_liens'] = array(
		'field' => array(
			'id_region' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet' => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet' => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu' => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_region,id_objet,objet',
			'KEY id_region' => 'id_region',
		),
	);

	$tables['spip_geo_departements_liens'] = array(
		'field' => array(
			'id_departement' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet' => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet' => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu' => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_departement,id_objet,objet',
			'KEY id_departement' => 'id_departement',
		),
	);

	$tables['spip_geo_arrondissements_liens'] = array(
		'field' => array(
			'id_arrondissement' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet' => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet' => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu' => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_arrondissement,id_objet,objet',
			'KEY id_arrondissement' => 'id_arrondissement',
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

	return $tables;
}

function geographie_lister_tables_noexport($liste) {
	$liste[] = 'spip_geo_communes';
	$liste[] = 'spip_geo_arrondissements';
	$liste[] = 'spip_geo_departements';
	$liste[] = 'spip_geo_regions';
	$liste[] = 'spip_geo_pays';

	return $liste;
}

global $IMPORT_tables_noerase;
$IMPORT_tables_noerase[] = 'spip_geo_communes';
$IMPORT_tables_noerase[] = 'spip_geo_arrondissements';
$IMPORT_tables_noerase[] = 'spip_geo_departements';
$IMPORT_tables_noerase[] = 'spip_geo_regions';
$IMPORT_tables_noerase[] = 'spip_geo_pays';
