<?php
/**
 * Fichier gérant les importations en base de donnée.
 *
 * @plugin     Continents
 * @copyright  2013 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Continents\Base
 */

//
// Structure des tables
//
if (!defined("_ECRIRE_INC_VERSION"))
	return;

function continents_declarer_tables_interfaces($interface) {

	// -- Table des tables ----------------------------------------------------
	$interface['table_des_tables']['continents'] = 'continents';

	return $interface;
}

function continents_declarer_tables_principales($tables_principales) {
	$spip_continents = array(
		"id_continent" => "SMALLINT NOT NULL",
		"nom" => "varchar(255) NOT NULL",
		"code_onu" => "SMALLINT NOT NULL",
		"code_iso_a2" => "varchar(2) NOT NULL default ''",
		"code_iso_a3" => "varchar(3) NOT NULL default ''",
		"latitude" => 'text not null default ""',
		"longitude" => 'text not null default ""',
		"zoom" => 'text not null default ""',
		"maj" => "TIMESTAMP"
	);

	$spip_continents_key = array(
		"PRIMARY KEY" => "id_continent",
		"KEY code_onu" => "code_onu"
	);

	$tables_principales['spip_continents'] = array(
		'field' => &$spip_continents,
		'key' => &$spip_continents_key,
		'join' => array(
			'id_continent' => 'id_continent'
		)
	);

	$tables_principales['spip_pays'] = array(
		'field' => array(
			'id_continent' => "SMALLINT NOT NULL"
		),
		'key' => array(
			'KEY id_continent' => "id_continent"
		),
		'join' => array(
			'id_continent' => "id_continent"
		)
	);

	return $tables_principales;
}
