<?php
/**
 * Plugin Coordonnees pour Spip 2.1
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

function coordonnees_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['adresses'] = 'adresses';
	$interface['table_des_tables']['numeros'] = 'numeros';
	$interface['table_des_tables']['emails'] = 'emails';
	
	$interface['tables_jointures']['spip_auteurs'][] = 'adresses_liens';
	$interface['tables_jointures']['spip_adresses'][] = 'adresses_liens';
	
	$interface['tables_jointures']['spip_auteurs'][] = 'numeros_liens';
	$interface['tables_jointures']['spip_numeros'][] = 'numeros_liens';
	
	$interface['tables_jointures']['spip_auteurs'][] = 'emails_liens';
	$interface['tables_jointures']['spip_emails'][] = 'emails_liens';

	$interface['table_des_traitements']['VILLE'][] = _TRAITEMENT_TYPO;

	return $interface;
}


function coordonnees_declarer_tables_principales($tables_principales){

	//-- Table adresses ------------------------------------------
	$adresses = array(
		"id_adresse" => "bigint(21) NOT NULL auto_increment",
		"titre" => "VARCHAR(255) DEFAULT '' NOT NULL", // perso, pro, vacance...
		"voie" => "tinytext DEFAULT '' NOT NULL", // p. ex. 21 rue de cotte
		"complement" => "tinytext DEFAULT '' NOT NULL", // p. ex. 3? ?tage
		"boite_postale" => "VARCHAR(10) DEFAULT '' NOT NULL", 
		"code_postal" => "VARCHAR(10) DEFAULT '' NOT NULL",
		"ville" => "tinytext DEFAULT '' NOT NULL",
		"pays" => "varchar(2) not null default ''",
		"maj" => "TIMESTAMP"
		);
	$adresses_key = array(
		"PRIMARY KEY"	=> "id_adresse"
		);
	$tables_principales['spip_adresses'] =
		array(
			'field' => &$adresses, 'key' => &$adresses_key, 'join' => &$adresses_join);

	//-- Table numeros ------------------------------------------
	$numeros = array(
		"id_numero" => "bigint(21) NOT NULL auto_increment",
		"titre" => "VARCHAR(255) DEFAULT '' NOT NULL", // peut etre domicile, bureau, portable
		"numero" => "tinytext DEFAULT '' NOT NULL",
		"maj" => "TIMESTAMP"
		);
	$numeros_key = array(
		"PRIMARY KEY" => "id_numero"
		);
	$tables_principales['spip_numeros'] =
		array('field' => &$numeros, 'key' => &$numeros_key, 'join' => &$numeros_join);

	//-- Table emails ------------------------------------------
	$emails = array(
		"id_email" => "bigint(21) NOT NULL auto_increment",
		"titre" => "VARCHAR(255) DEFAULT '' NOT NULL", // peut etre perso, boulot, etc.
		"email" => "VARCHAR(40) DEFAULT '' NOT NULL",
		"maj" => "TIMESTAMP"
		);
	$emails_key = array(
		"PRIMARY KEY"	=> "id_email",
		"KEY email"	=> "email" // on ne met pas unique pour le cas ou 2 contacts partagent le meme mail g?n?rique
		);
	$tables_principales['spip_emails'] =
		array('field' => &$emails, 'key' => &$emails_key, 'join' => &$emails_join);


	return $tables_principales;

}



function coordonnees_declarer_tables_auxiliaires($tables_auxiliaires){

	//-- Table adresses_liens ---------------------------------------
	$adresses_liens = array(
		"id_adresse" => "BIGINT(21) NOT NULL",
		"id_objet" => "BIGINT(21) NOT NULL",
		"objet" => "varchar(25) NOT NULL" // peut etre un compte ou un contact
	);
	$adresses_liens_key = array(
		"PRIMARY KEY" => "id_adresse, id_objet, objet",
		"KEY id_adresse" => "id_adresse"
	);
	$tables_auxiliaires['spip_adresses_liens'] =
		array('field' => &$adresses_liens, 'key' => &$adresses_liens_key);


	//-- Table numeros_liens ------------------------------------------
	$numeros_liens = array(
		"id_numero" => "bigint(21) NOT NULL DEFAULT 0",
		"id_objet" => "bigint(21) NOT NULL DEFAULT 0", 
		"objet" => "varchar(25) NOT NULL" // peut etre un contact ou un compte
		);
	$numeros_liens_key = array(
		"PRIMARY KEY" => "id_numero, id_objet, objet",
		"KEY id_numero" => "id_numero"
		);
	$tables_auxiliaires['spip_numeros_liens'] =
		array('field' => &$numeros_liens, 'key' => &$numeros_liens_key);


	//-- Table emails_liens ------------------------------------------
	$emails_liens = array(
		"id_email" => "bigint(21) NOT NULL DEFAULT 0",
		"id_objet" => "bigint(21) NOT NULL DEFAULT 0", 
		"objet" => "varchar(25) NOT NULL" // peut etre un contact ou un compte
		);
	$emails_liens_key = array(
		"PRIMARY KEY" => "id_email, id_objet, objet",
		"KEY id_email" => "id_email"
		);
	$tables_auxiliaires['spip_emails_liens'] =
		array('field' => &$emails_liens, 'key' => &$emails_liens_key);

	
	return $tables_auxiliaires;
}

?>
