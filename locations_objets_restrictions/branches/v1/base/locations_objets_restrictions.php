<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Locations d&#039;objets - restrictions
 * @copyright  2019
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Locations_objets_restrictions\Pipelines
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
function locations_objets_restrictions_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['restrictions'] = 'restrictions';

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
function locations_objets_restrictions_declarer_tables_objets_sql($tables) {

	$tables['spip_restrictions'] = [
		'type' => 'restriction',
		'principale' => 'oui',
		'field'=> [
			'id_restriction'     => 'bigint(21) NOT NULL',
			'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
			'descriptif'         => 'text NOT NULL DEFAULT ""',
			"type_restriction" => "varchar(20) NOT NULL DEFAULT ''",
			"valeurs_restriction" => "text NOT NULL DEFAULT ''",
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'                => 'TIMESTAMP'
		],
		'key' => [
			'PRIMARY KEY'        => 'id_restriction',
		],
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => ['id_restriction_source', 'titre', 'descriptif', 'type_restriction', 'valeurs_restriction'],
		'champs_versionnes' => ['id_restriction_source', 'titre', 'descriptif', 'type_restriction', 'valeurs_restriction'],
		'rechercher_champs' => ["titre" => 10, "descriptif" => 8],
		'tables_jointures'  => ['spip_restrictions_liens'],
	];

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
function locations_objets_restrictions_declarer_tables_auxiliaires($tables) {

	$tables['spip_restrictions_liens'] = [
		'field' => [
			'id_restriction'     => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
			"rang_lien"          => "int(4) NOT NULL DEFAULT '0'",
		],
		'key' => [
			'PRIMARY KEY'        => 'id_restriction,id_objet,objet',
			'KEY id_restriction' => 'id_restriction',
			'KEY rang_lien'      => 'rang_lien',
		]
	];

	return $tables;
}
