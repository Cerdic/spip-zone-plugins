<?php
/**
 * Plugin Gabarits pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function gabarits_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	// pour pouvoir faires des BOUCLES(GABARITS)
	$interface['table_des_tables']['gabarits']='gabarits';
	
	return $interface;
}

function gabarits_declarer_tables_principales($tables_principales){
	//-- Table GABARITS ------------------------------------------
	$gabarits = array(
		"id_gabarit"	=> "bigint(21) NOT NULL",
		"id_auteur" => "bigint(21) NOT NULL",
		"objet"	=> "varchar(21) DEFAULT '' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
		"titre"	=> "text NOT NULL",
		"texte"	=> "text NOT NULL",
		"date"	=> "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"
	);
	
	$gabarits_key = array(
		"PRIMARY KEY"	=> "id_gabarit",
		"KEY objet" => "objet",
		"KEY id_objet" => "id_objet",
		"KEY id_auteur"	=> "id_auteur",
	);
	
	$tables_principales['spip_gabarits'] = array(
		'field' => &$gabarits,
		'key' => &$gabarits_key
	);

	return $tables_principales;
}

?>