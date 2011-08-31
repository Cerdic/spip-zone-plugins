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
	
	$interface['table_des_tables']['livrables'] = 'livrables';

	return $interface;
}

function livrables_declarer_tables_principales($tables_principales){
	$spip_livrables = array(
		"id_livrable" 			=> "bigint(21) NOT NULL",
		"id_projet"				=> "bigint(21) NOT NULL",
		"url"					=> "varchar(255) DEFAULT '' NOT NULL",
		"statut_client"			=> "varchar(10) DEFAULT '' NOT NULL",
		"statut_atelier"		=> "varchar(10) DEFAULT '' NOT NULL",
		"titre" 				=> "varchar(255) DEFAULT '' NOT NULL",
		"descriptif" 			=> "longtext DEFAULT '' NOT NULL",
		"maj" 					=> "TIMESTAMP"
	);
	
	$spip_livrables_key = array(
		"PRIMARY KEY" 			=> "id_livrable",
		"KEY id_projet" 		=> "id_projet",
		"KEY url" 				=> "url",
		"KEY statut_client"		=> "statut_client",
		"KEY statut_atelier"	=> "statut_atelier"
	);
	
	$tables_principales['spip_livrables'] = 
		array('field' => &$spip_livrables, 'key' => &$spip_livrables_key);

	// ajout du champ id_livrable dans la table spip_tickets
	$tables_principales['spip_tickets']['field']['id_livrable'] = "bigint(21) NOT NULL";		
	
	return $tables_principales;
}

function livrables_declarer_tables_auxiliaires($tables_auxiliaires){
    $spip_livrables_liens = array(
        "id_livrable"	=> "BIGINT(21) NOT NULL",
        "id_objet"   	=> "BIGINT(21) NOT NULL",
        "objet"      	=> "VARCHAR(25) NOT NULL",
    );
    $spip_livrables_liens_key = array(
        "PRIMARY KEY"    => "id_livrable, id_objet, objet",
		"KEY id_livrable" => "id_livrable"
    );
	$tables_auxiliaires['spip_livrables_liens'] =
		array('field' => &$spip_livrables_liens, 'key' => &$spip_livrables_liens_key);

	return $tables_auxiliaires;
}


?>