<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function menus_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['menus']='menus';
	$interface['table_des_tables']['menus_entrees']='menus_entrees';
	
	// Titres
	$interface['table_titre']['menus'] = 'titre, "" as lang';
	
	return $interface;
}

function menus_declarer_tables_principales($tables_principales){
	//-- Table menus -----------------------------------------------------------
	$menus = array(
		"id_menu" => "bigint(21) NOT NULL",
		"id_menus_entree" => "bigint(21) DEFAULT '0' NOT NULL",
		"titre" => "text DEFAULT '' NOT NULL",
		"identifiant" => "varchar(255) default '' not null",
		"css" => "tinytext DEFAULT '' NOT NULL"
	);
	
	$menus_cles = array(
		"PRIMARY KEY" => "id_menu",
		"KEY id_menus_entree" => "id_menus_entree"
	);
	
	$tables_principales['spip_menus'] = array(
		'field' => &$menus,
		'key' => &$menus_cles
	);
	
	// Table menus_elements ----------------------------------------------------
	$menus_entrees = array(
		"id_menus_entree" => "bigint(21) NOT NULL",
		"id_menu" => "bigint(21) DEFAULT '0' NOT NULL",
		"rang" => "smallint DEFAULT '1' NOT NULL",
		"type_entree" => "tinytext DEFAULT '' NOT NULL",
		"parametres" => "text DEFAULT '' NOT NULL"
	);
	
	$menus_entrees_cles = array(
		"PRIMARY KEY" => "id_menus_entree",
		"KEY id_menu" => "id_menu"
	);
	
	$tables_principales['spip_menus_entrees'] = array(
		'field' => &$menus_entrees,
		'key' => &$menus_entrees_cles
	);

	return $tables_principales;
}

?>
