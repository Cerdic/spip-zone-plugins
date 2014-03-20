<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

function accesrestreint_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_auteurs'][] = 'zones_auteurs';
	$interface['tables_jointures']['spip_zones'][] = 'zones_auteurs';
	
	$interface['tables_jointures']['spip_rubriques'][] = 'zones_rubriques';
	$interface['tables_jointures']['spip_zones'][] = 'zones_rubriques';
	
	//-- Table des tables ----------------------------------------------------
	
	$interface['table_des_tables']['zones']='zones';

	/*if (version_compare($spip_version_code,'1.9250','<')) {
		$table_des_tables['zones_rubriques']='zones_rubriques'; 
		$table_des_tables['zones_auteurs']='zones_auteurs'; 
	}*/
	return $interface;
}

function accesrestreint_declarer_tables_principales($tables_principales){
	$spip_zones = array(
		"id_zone" 	=> "bigint(21) NOT NULL",
		"titre" 	=> "varchar(255) DEFAULT '' NOT NULL",
		"descriptif" 	=> "text DEFAULT '' NOT NULL",
		"publique" 	=> "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
		"privee" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
		"maj" 		=> "TIMESTAMP");
	
	$spip_zones_key = array(
		"PRIMARY KEY" => "id_zone");
	
	$tables_principales['spip_zones'] = array(
		'field' => &$spip_zones,
		'key' => &$spip_zones_key);
		
	return $tables_principales;
}

function accesrestreint_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_zones_auteurs = array(
		"id_zone" 	=> "bigint(21) NOT NULL",
		"id_auteur" 	=> "bigint(21) NOT NULL");
	
	$spip_zones_auteurs_key = array(
		"PRIMARY KEY" 	=> "id_zone, id_auteur",
		"KEY id_auteur" => "id_auteur");
	
	$tables_auxiliaires['spip_zones_auteurs'] = array(
		'field' => &$spip_zones_auteurs,
		'key' => &$spip_zones_auteurs_key);
	
	$spip_zones_rubriques = array(
		"id_zone" 	=> "bigint(21) NOT NULL",
		"id_rubrique" 	=> "bigint(21) NOT NULL");
	
	$spip_zones_rubriques_key = array(
		"PRIMARY KEY" 	=> "id_zone, id_rubrique",
		"KEY id_rubrique" => "id_rubrique");
	
	$tables_auxiliaires['spip_zones_rubriques'] = array(
		'field' => &$spip_zones_rubriques,
		'key' => &$spip_zones_rubriques_key);
	return $tables_auxiliaires;
}

?>
