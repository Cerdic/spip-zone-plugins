<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Projets - Sites internet
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function projets_sites_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['projets_sites'] = 'projets_sites';

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
function projets_sites_declarer_tables_objets_sql($tables) {

	$tables['spip_projets_sites'] = array(
		'type' => 'projet_site',
		'principale' => "oui", 
		'table_objet_surnoms' => array('projetssite'), // table_objet('projet_site') => 'projets_sites' 
		'field'=> array(
			"id_site"            => "bigint(21) NOT NULL",
			"logiciel_nom"       => "varchar(25) NOT NULL DEFAULT ''",
			"logiciel_version"   => "varchar(25) NOT NULL DEFAULT ''",
			"type_site"          => "varchar(4) NOT NULL DEFAULT ''",
			"date_creation"      => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"fo_url"             => "varchar(255) NOT NULL DEFAULT ''",
			"fo_login"           => "varchar(25) NOT NULL DEFAULT ''",
			"fo_password"        => "varchar(25) NOT NULL DEFAULT ''",
			"bo_url"             => "varchar(255) NOT NULL DEFAULT ''",
			"bo_login"           => "varchar(25) NOT NULL DEFAULT ''",
			"bo_password"        => "varchar(25) NOT NULL DEFAULT ''",
			"applicatif_serveur" => "varchar(255) NOT NULL DEFAULT ''",
			"applicatif_path"    => "varchar(255) NOT NULL DEFAULT ''",
			"applicatif_surveillance" => "varchar(255) NOT NULL DEFAULT ''",
			"svn_path"           => "varchar(255) NOT NULL DEFAULT ''",
			"svn_trac"           => "varchar(255) NOT NULL DEFAULT ''",
			"sas_dpi"            => "varchar(255) NOT NULL DEFAULT ''",
			"sgbd_type"          => "varchar(25) NOT NULL DEFAULT ''",
			"sgbd_serveur"       => "varchar(255) NOT NULL DEFAULT ''",
			"sgbd_nom"           => "varchar(50) NOT NULL DEFAULT ''",
			"sgbd_login"         => "varchar(25) NOT NULL DEFAULT ''",
			"sgbd_password"      => "varchar(25) NOT NULL DEFAULT ''",
			"sso"                => "varchar(25) NOT NULL DEFAULT ''",
			"perimetre_acces"    => "mediumtext NOT NULL DEFAULT ''",
			"statistiques"       => "mediumtext NOT NULL DEFAULT ''",
			"moteur_recherche"   => "mediumtext NOT NULL DEFAULT ''",
			"autres_outils"      => "mediumtext NOT NULL DEFAULT ''",
			"remarques"          => "text NOT NULL DEFAULT ''",
			"date_creation"      => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_site",
		),
		'titre' => "type_site AS titre, '' AS lang",
		'date' => "date_creation",
		'champs_editables'  => array('logiciel_nom', 'logiciel_version', 'type_site', 'date_creation', 'fo_url', 'fo_login', 'fo_password', 'bo_url', 'bo_login', 'bo_password', 'applicatif_serveur', 'applicatif_path', 'applicatif_surveillance', 'svn_path', 'svn_trac', 'sas_dpi', 'sgbd_fieldset', 'sgbd_type', 'sgbd_serveur', 'sgbd_nom', 'sgbd_login', 'sgbd_password', 'sso', 'perimetre_acces', 'statistiques', 'moteur_recherche', 'autres_outils', 'remarques'),
		'champs_versionnes' => array('date_creation', 'fo_url', 'bo_url', 'applicatif_serveur', 'sgbd_type', 'sgbd_password', 'statistiques', 'moteur_recherche', 'autres_outils', 'remarques'),
		'rechercher_champs' => array("logiciel_nom" => 8, "logiciel_version" => 3, "type_site" => 3, "fo_url" => 5, "applicatif_serveur" => 6),
		'tables_jointures'  => array('spip_projets_sites_liens'),
		

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
function projets_sites_declarer_tables_auxiliaires($tables) {

	$tables['spip_projets_sites_liens'] = array(
		'field' => array(
			"id_site"            => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_site,id_objet,objet",
			"KEY id_site"        => "id_site"
		)
	);

	return $tables;
}


?>