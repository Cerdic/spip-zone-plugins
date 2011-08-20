<?php

/*
 * Plugin Livrables
 * Licence GPL (c) 2011 Cyril Marion
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function livrables_declarer_tables_interfaces($interface){
	/**
	 * Futures jointures avec projets
	 *
	$interface['tables_jointures']['spip_livrables'][] = 'livrables_liens';
	$interface['tables_jointures']['spip_projets'][] = 'livrables_liens';
	 **/
	
	//-- Table des tables ----------------------------------------------------
	
	$interface['table_des_tables']['livrables']='livrables';

	return $interface;
}

function livrables_declarer_tables_principales($tables_principales){
	$spip_livrables = array(
		"id_livrable" 	=> "bigint(21) NOT NULL",
		"titre" 		=> "varchar(255) DEFAULT '' NOT NULL",
		"descriptif" 	=> "longtext DEFAULT '' NOT NULL",
		"url"			=> "text DEFAULT '' NOT NULL",
		"maj" 			=> "TIMESTAMP");
	
	$spip_livrables_key = array(
		"PRIMARY KEY" => "id_livrable",
		"KEY url" => "url");
	
	$tables_principales['spip_livrables'] = array(
		'field' => &$spip_livrables,
		'key' => &$spip_livrables_key);
		
	return $tables_principales;
}

function livrables_declarer_tables_auxiliaires($tables_auxiliaires){
    $livrables_liens = array(
        "id_livrable"	=> "BIGINT(21) NOT NULL",
        "id_objet"   	=> "BIGINT(21) NOT NULL",
        "objet"      	=> "VARCHAR(25) NOT NULL",
    );
    $livrables_liens_key = array(
        "PRIMARY KEY"    => "id_livrable, id_objet, objet",
		"KEY id_livrable" => "id_livrable"
    );
	$tables_auxiliaires['livrables_liens'] =
		array('field' => &$livrables_liens, 'key' => &$livrables_liens_key);

	return $tables_auxiliaires;
}


?>