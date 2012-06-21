<?php 
/**
 * 
 * Plugin auteurs_syndic
 * par kent1
 * Déclaration des tables
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function auteurs_syndic_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_auteurs_syndic = array(
			"id_auteur"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_syndic"	=> "bigint(21) DEFAULT '0' NOT NULL");
	
	$spip_auteurs_syndic_key = array(
			"PRIMARY KEY"	=> "id_auteur, id_syndic",
			"KEY id_article"	=> "id_syndic");
	
	$tables_auxiliaires['spip_auteurs_syndic'] = array(
		'field' => &$spip_auteurs_syndic,
		'key' => &$spip_auteurs_syndic_key);
	
	return $tables_auxiliaires;
}

function auteurs_syndic_declarer_tables_interfaces($interfaces){
	$interfaces['tables_jointures']['spip_auteurs'][]= 'auteurs_syndic';
	$interfaces['tables_jointures']['spip_syndic'][]= 'auteurs_syndic';
	return $interfaces;
}

?>