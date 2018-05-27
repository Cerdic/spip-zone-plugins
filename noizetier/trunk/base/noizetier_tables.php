<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des informations tierces (alias, traitements, jointures, etc)
 * sur les tables de la base de données modifiées ou ajoutées par le plugin.
 *
 * Le plugin se contente de déclarer les alias des tables et quelques traitements.
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interface
 * 		Tableau global des informations tierces sur les tables de la base de données
 * @return array
 *		Tableau fourni en entrée et mis à jour avec les nouvelles informations
 */
function noizetier_declarer_tables_interfaces($interface) {

	// Les tables : permet d'appeler une boucle avec le *type* de la table uniquement
	$interface['table_des_tables']['noizetier_pages'] = 'noizetier_pages';
	$interface['table_des_tables']['types_noisettes'] = 'types_noisettes';
	$interface['table_des_tables']['noisettes'] = 'noisettes';

	// Les traitements
	// - table spip_noizetier_pages : on desérialise les tableaux et on passe _T_ou_typo
	$interface['table_des_traitements']['BLOCS_EXCLUS']['noizetier_pages'] = 'unserialize(%s)';
	$interface['table_des_traitements']['BRANCHE']['noizetier_pages'] = 'unserialize(%s)';
	$interface['table_des_traitements']['NECESSITE']['noizetier_pages'] = 'unserialize(%s)';
	$interface['table_des_traitements']['NOM']['noizetier_pages'] = '_T_ou_typo(%s)';
	$interface['table_des_traitements']['DESCRIPTION']['noizetier_pages'] = '_T_ou_typo(%s)';
	// - table spip_types_noisettes : on desérialise les tableaux et on passe _T_ou_typo
	$interface['table_des_traitements']['PARAMETRES']['types_noisettes'] = 'unserialize(%s)';
	$interface['table_des_traitements']['CONTEXTE']['types_noisettes'] = 'unserialize(%s)';
	$interface['table_des_traitements']['NECESSITE']['types_noisettes'] = 'unserialize(%s)';
	$interface['table_des_traitements']['NOM']['types_noisettes'] = '_T_ou_typo(%s)';
	$interface['table_des_traitements']['DESCRIPTION']['types_noisettes'] = '_T_ou_typo(%s)';

	return $interface;
}

/**
 * Déclaration des nouvelles tables de la base de données propres au plugin.
 *
 * Le plugin déclare trois nouvelles tables qui sont :
 *
 * - `spip_noizetier_pages`, qui contient les éléments descriptifs des pages et compositions,
 * - `spip_types_noisettes`, qui contient les éléments descriptifs des types de noisette disponibles,
 * - `spip_noisettes`, qui contient l'affectation des noisettes dans les pages concernées.
 *
 * @pipeline declarer_tables_principales
 *
 * @param array $tables_principales
 *		Tableau global décrivant la structure des tables de la base de données
 * @return array
 *		Tableau fourni en entrée et mis à jour avec les nouvelles déclarations
 */
function noizetier_declarer_tables_principales($tables_principales) {

	// Table spip_noizetier_pages
	$pages = array(
		'page'           => "varchar(255) DEFAULT '' NOT NULL",
		'type'           => "varchar(127) DEFAULT '' NOT NULL",
		'composition'    => "varchar(127) DEFAULT '' NOT NULL",
		'nom'            => "text DEFAULT '' NOT NULL",
		'description'    => "text DEFAULT '' NOT NULL",
		'icon'           => "varchar(255) DEFAULT '' NOT NULL",
		'blocs_exclus'   => "text DEFAULT '' NOT NULL",
		'necessite'      => "text DEFAULT '' NOT NULL",
		'est_active'     => "varchar(3) DEFAULT 'oui' NOT NULL",
		'branche'        => "text DEFAULT '' NOT NULL",
		'est_page_objet' => "varchar(3) DEFAULT 'oui' NOT NULL",
		'est_virtuelle'  => "varchar(3) DEFAULT 'non' NOT NULL",
		'image_exemple'  => "varchar(255) DEFAULT '' NOT NULL",
		'class'          => "varchar(255) DEFAULT '' NOT NULL",
		'configuration'  => "varchar(255) DEFAULT '' NOT NULL",
		'signature'      => "varchar(32) DEFAULT '' NOT NULL",
		"maj"			 => "timestamp",
	);

	$pages_cles = array(
		'PRIMARY KEY'        => 'page',
		'KEY type'           => 'type',
		'KEY composition'    => 'composition',
		"KEY est_page_objet" => "est_page_objet",
		"KEY est_virtuelle"  => "est_virtuelle",
	);

	$tables_principales['spip_noizetier_pages'] = array(
		'field' => &$pages,
		'key' => &$pages_cles,
	);

	// Table spip_types_noisettes
	$types_noisettes = array(
		'plugin'         => "varchar(30) DEFAULT '' NOT NULL",
		'type_noisette'  => "varchar(255) DEFAULT '' NOT NULL",
		'type'           => "varchar(127) DEFAULT '' NOT NULL",
		'composition'    => "varchar(127) DEFAULT '' NOT NULL",
		'nom'            => "text DEFAULT '' NOT NULL",
		'description'    => "text DEFAULT '' NOT NULL",
		'icon'           => "varchar(255) DEFAULT '' NOT NULL",
		'necessite'      => "text DEFAULT '' NOT NULL",
		'actif'          => "varchar(3) DEFAULT 'oui' NOT NULL",
		'conteneur'      => "varchar(3) DEFAULT 'non' NOT NULL",
		'contexte'       => "text DEFAULT '' NOT NULL",
		'ajax'           => "varchar(6) DEFAULT '' NOT NULL",
		'inclusion'      => "varchar(9) DEFAULT '' NOT NULL",
		'parametres'     => "text DEFAULT '' NOT NULL",
		'signature'      => "varchar(32) DEFAULT '' NOT NULL",
		"maj"			 => "timestamp",
	);

	$types_noisettes_cles = array(
		'PRIMARY KEY'     => 'plugin, type_noisette',
		'KEY type'        => 'type',
		'KEY composition' => 'composition',
		'KEY actif'       => 'actif',
		'KEY ajax'        => 'ajax',
		'KEY inclusion'   => 'inclusion',
	);

	$tables_principales['spip_types_noisettes'] = array(
		'field' => &$types_noisettes,
		'key' => &$types_noisettes_cles,
	);

	// Table spip_noisettes
	$noisettes = array(
		'id_noisette'   => 'bigint(21) NOT NULL',
		'plugin'        => "varchar(30) DEFAULT '' NOT NULL",
		'id_conteneur'  => "varchar(255) DEFAULT '' NOT NULL",
		'rang_noisette' => "smallint DEFAULT 1 NOT NULL",
		'type'          => "varchar(127) DEFAULT '' NOT NULL",
		'composition'   => "varchar(127) DEFAULT '' NOT NULL",
		'objet'         => 'varchar(25) NOT NULL default ""',
		'id_objet'      => 'bigint(21) NOT NULL default 0',
		'bloc'          => "varchar(255) DEFAULT '' NOT NULL",
		'type_noisette' => "varchar(255) DEFAULT '' NOT NULL",
		'est_conteneur' => "varchar(3) DEFAULT 'non' NOT NULL",
		'parametres'    => "text DEFAULT '' NOT NULL",
		'balise'        => "varchar(6) DEFAULT 'defaut' NOT NULL",
		'css'           => "tinytext DEFAULT '' NOT NULL",
	);

	$noisettes_cles = array(
		'PRIMARY KEY'       => 'id_noisette',
		'KEY plugin'        => 'plugin',
		'KEY id_conteneur'  => 'id_conteneur',
		'KEY type'          => 'type',
		'KEY composition'   => 'composition',
		'KEY bloc'          => 'bloc',
		'KEY type_noisette' => 'type_noisette',
		'KEY objet'         => 'objet',
		'KEY id_objet'      => 'id_objet',
	);

	$tables_principales['spip_noisettes'] = array(
		'field' => &$noisettes,
		'key'   => &$noisettes_cles,
		'join'  => array(
			'id_noisette'   => 'id_noisette',
			'type_noisette' => 'type_noisette',
		),
	);

	return $tables_principales;
}
