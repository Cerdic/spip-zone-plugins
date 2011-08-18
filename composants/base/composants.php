<?php

/*
 * Plugin Composants
 * Licence GPL (c) 2011 Cyril Marion
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function composants_declarer_tables_interfaces($interface){
	/**
	 * Futures jointures avec projets
	 *
	$interface['tables_jointures']['spip_composants'][] = 'composants_liens';
	$interface['tables_jointures']['spip_projets'][] = 'composants_liens';
	 **/
	
	//-- Table des tables ----------------------------------------------------
	
	$interface['table_des_tables']['composants']='composants';

	return $interface;
}

function composants_declarer_tables_principales($tables_principales){
	$spip_composants = array(
		"id_composant" 	=> "bigint(21) NOT NULL",
		"titre" 		=> "varchar(255) DEFAULT '' NOT NULL",
		"descriptif" 	=> "longtext DEFAULT '' NOT NULL",
		"url"			=> "text DEFAULT '' NOT NULL",
		"maj" 			=> "TIMESTAMP");
	
	$spip_composants_key = array(
		"PRIMARY KEY" => "id_composant");
	
	$tables_principales['spip_composants'] = array(
		'field' => &$spip_composants,
		'key' => &$spip_composants_key);
		
	return $tables_principales;
}

function composants_declarer_tables_auxiliaires($tables_auxiliaires){
    $composants_liens = array(
        "id_composant"	=> "BIGINT(21) NOT NULL",
        "id_objet"   	=> "BIGINT(21) NOT NULL",
        "objet"      	=> "VARCHAR(25) NOT NULL",
    );
    $composants_liens_key = array(
        "PRIMARY KEY"    => "id_composant, id_objet, objet",
		"KEY id_organisation" => "id_composant"
    );
	$tables_auxiliaires['spip_composants_liens'] =
		array('field' => &$composants_liens, 'key' => &$composants_liens_key);

	return $tables_auxiliaires;
}


?>