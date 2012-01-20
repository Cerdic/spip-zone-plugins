<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function noizetier_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['noisettes']='noisettes';
	
	return $interface;
}

function noizetier_declarer_tables_principales($tables_principales){
	//-- Table noisettes -----------------------------------------------------------
	$noisettes = array(
		"id_noisette" => "bigint(21) NOT NULL",
		"rang" => "smallint DEFAULT '1' NOT NULL",
		"type" => "tinytext DEFAULT '' NOT NULL",
		"composition" => "tinytext DEFAULT '' NOT NULL",
		"bloc" => "tinytext DEFAULT '' NOT NULL",
		"noisette" => "tinytext DEFAULT '' NOT NULL",
		"parametres" => "text DEFAULT '' NOT NULL",
		"css" => "tinytext DEFAULT '' NOT NULL"
	);
	
	$noisettes_cles = array(
		"PRIMARY KEY" => "id_noisette"
	);
	
	$tables_principales['spip_noisettes'] = array(
		'field' => &$noisettes,
		'key' => &$noisettes_cles
	);
	
	return $tables_principales;
}

?>
