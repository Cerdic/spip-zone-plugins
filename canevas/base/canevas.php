<?php
/**
 * Plugin Canevas pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function canevas_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	// pour pouvoir faires des BOUCLES(CANEVAS)
	$interface['table_des_tables']['canevas']='canevas';
	
	return $interface;
}

function canevas_declarer_tables_principales($tables_principales){
	//-- Table CANEVAS ------------------------------------------
	$canevas = array(
		"id_canevas"	=> "bigint(21) NOT NULL",
		"id_auteur" => "bigint(21) NOT NULL",
		"objet"	=> "varchar(21) DEFAULT '' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
		"titre"	=> "text NOT NULL",
		"texte"	=> "text NOT NULL",
		"date"	=> "TIMESTAMP"
	);
	
	$canevas_key = array(
		"PRIMARY KEY"	=> "id_canevas",
		"KEY objet" => "objet",
		"KEY id_objet" => "id_objet",
		"KEY id_auteur"	=> "id_auteur",
	);
	
	$tables_principales['spip_canevas'] = array(
		'field' => &$canevas,
		'key' => &$canevas_key
	);

	return $tables_principales;
}

?>