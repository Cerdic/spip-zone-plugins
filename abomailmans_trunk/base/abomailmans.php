<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
include_spip('base/create');


/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */

function abomailmans_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	 
		if (version_compare($current_version,'0.0','<')){
			include_spip('base/abomailmans');
			creer_base();
		}
		if (version_compare($current_version,'0.30','<')){
			sql_alter("TABLE spip_abomailmans ADD `lang` varchar(10) DEFAULT ' ' NOT NULL AFTER `email_sympa`");
			ecrire_meta('agenda_base_version',$current_version=0.30,'non');
		}
		if (version_compare($current_version,'0.31','<')){
			sql_alter("TABLE spip_abomailmans ADD `email_unsubscribe` varchar(255) DEFAULT ' ' NOT NULL AFTER `email`");
			sql_alter("TABLE spip_abomailmans ADD `email_subscribe` varchar(255) DEFAULT ' ' NOT NULL AFTER `email`");
			ecrire_meta('agenda_base_version',$current_version=0.31,'non');
		}
		if (version_compare($current_version,'0.32','<')){
			sql_alter("TABLE spip_abomailmans ADD `date_envoi` TIMESTAMP AFTER `maj`");
			sql_alter("TABLE spip_abomailmans ADD `modele_defaut` varchar(255) DEFAULT ' ' NOT NULL AFTER `email_unsubscribe`");
			sql_alter("TABLE spip_abomailmans ADD `periodicite` varchar(255) DEFAULT ' ' NOT NULL AFTER `email_unsubscribe`");
			ecrire_meta('agenda_base_version',$current_version=0.32,'non');
		}
		if (version_compare($current_version,'0.33','<')){
			maj_tables('spip_abomailmans');
			ecrire_meta('agenda_base_version',$current_version=0.33,'non');
		}
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function abomailmans_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_abomailmans");
	effacer_meta($nom_meta_base_version);
}



function abomailmans_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['abomailmans'] = 'abomailmans';
	return $interface;
}

function abomailmans_declarer_tables_principales($tables_principales){
	$spip_abomailmans = array(
		"id_abomailman" => "bigint(21) NOT NULL",
		"titre" 	=> "varchar(255) NOT NULL",
		"descriptif" 	=> "text",
		"email"		=> "varchar(255)",
		"email_sympa"   => "varchar(255) DEFAULT ' ' NOT NULL",
		"email_subscribe"   => "varchar(255)",
		"email_unsubscribe" => "varchar(255)",
		"modele_defaut" => "varchar(255) DEFAULT ' ' NOT NULL",
		"periodicite" => "varchar(255) DEFAULT ' ' NOT NULL",
		"maj" 		=> "TIMESTAMP",
		"date_envoi" 	=> "TIMESTAMP",
		"lang"		=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"desactive"     => "tinyint(4) NOT NULL default '0'"
	);

	$spip_abomailmans_key = array(
	"PRIMARY KEY" => "id_abomailman");
 
	$tables_principales['spip_abomailmans'] = array(
	'field' => &$spip_abomailmans,
	'key' => &$spip_abomailmans_key);

 	 return $tables_principales;
}

?>