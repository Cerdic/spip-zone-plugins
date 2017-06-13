<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des informations tierces (alias, traitements, jointures, etc)
 * sur les tables de la base de données modifiées ou ajoutées par le plugin.
 *
 * Le plugin se contente de déclarer les alias des tables.
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interface
 * 		Tableau global des informations tierces sur les tables de la base de données
 * @return array
 *		Tableau fourni en entrée et mis à jour avec les nouvelles informations
 */
function noizetier_declarer_tables_interfaces($interface) {
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['noisettes'] = 'noisettes';
	$interface['table_des_tables']['noisettes_pages'] = 'noisettes_pages';

	return $interface;
}

/**
 * Déclaration des nouvelles tables de la base de données propres au plugin.
 *
 * Le plugin déclare deux nouvelles tables qui sont :
 *
 * - `spip_noisettes_pages`, qui contient les éléments descriptifs des pages et compositions,
 * - `spip_noisettes`, qui contient la desxcription de l'utilisation des noisettes dans les pages concernées.
 *
 * @pipeline declarer_tables_principales
 *
 * @param array $tables_principales
 *		Tableau global décrivant la structure des tables de la base de données
 * @return array
 *		Tableau fourni en entrée et mis à jour avec les nouvelles déclarations
 */
function noizetier_declarer_tables_principales($tables_principales) {

	// Table spip_noisettes_pages
	$pages = array(
		'page'           => "VARCHAR(255) DEFAULT '' NOT NULL",
		'type'           => "VARCHAR(127) DEFAULT '' NOT NULL",
		'composition'    => "VARCHAR(127) DEFAULT '' NOT NULL",
		'nom'            => "text DEFAULT '' NOT NULL",
		'description'    => "text DEFAULT '' NOT NULL",
		'icon'           => "VARCHAR(255) DEFAULT '' NOT NULL",
		'blocs_exclus'   => "text DEFAULT '' NOT NULL",
		'necessite'      => "text DEFAULT '' NOT NULL",
		'branche'        => "text DEFAULT '' NOT NULL",
		'est_page_objet' => "VARCHAR(3) DEFAULT 'oui' NOT NULL",
		'est_virtuelle'  => "VARCHAR(3) DEFAULT 'non' NOT NULL",
		'image_exemple'  => "VARCHAR(255) DEFAULT '' NOT NULL",
		'class'          => "VARCHAR(255) DEFAULT '' NOT NULL",
		'configuration'  => "VARCHAR(255) DEFAULT '' NOT NULL",
		'signature'      => "VARCHAR(32) DEFAULT '' NOT NULL",
	);

	$pages_cles = array(
		'PRIMARY KEY'        => 'page',
		'KEY type'           => 'type',
		'KEY composition'    => 'composition',
		"KEY est_page_objet" => "est_page_objet",
		"KEY est_virtuelle"  => "est_virtuelle",
	);

	$tables_principales['spip_noisettes_pages'] = array(
		'field' => &$pages,
		'key' => &$pages_cles,
	);

	// Table spip_noisettes
	$noisettes = array(
		'id_noisette' => 'bigint(21) NOT NULL',
		'rang'        => "smallint DEFAULT '1' NOT NULL",
		'type'        => "varchar(127) DEFAULT '' NOT NULL",
		'composition' => "varchar(127) DEFAULT '' NOT NULL",
		'objet'       => 'varchar(25) not null default ""',
		'id_objet'    => 'bigint(21) not null default 0',
		'bloc'        => "tinytext DEFAULT '' NOT NULL",
		'noisette'    => "tinytext DEFAULT '' NOT NULL",
		'parametres'  => "text DEFAULT '' NOT NULL",
		'balise'      => "varchar(6) DEFAULT 'defaut' NOT NULL",
		'css'         => "tinytext DEFAULT '' NOT NULL",
	);

	$noisettes_cles = array(
		'PRIMARY KEY'     => 'id_noisette',
		'KEY type'        => 'type(255)',
		'KEY composition' => 'composition(255)',
		'KEY bloc'        => 'bloc(255)',
		'KEY noisette'    => 'noisette(255)',
		'KEY objet'       => 'objet',
		'KEY id_objet'    => 'id_objet',
	);

	$tables_principales['spip_noisettes'] = array(
		'field' => &$noisettes,
		'key' => &$noisettes_cles,
	);

	return $tables_principales;
}
