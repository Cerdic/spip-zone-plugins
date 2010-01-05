<?php
/**
 * Plugin Comptes pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */


function comptes_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['comptes'] = 'comptes';
	$interface['table_des_tables']['contacts'] = 'contacts';
	$interface['table_des_tables']['adresses'] = 'adresses';
	$interface['table_des_tables']['numeros'] = 'numeros';
	$interface['table_des_tables']['champs'] = 'champs';
	
	/**
	 * Objectif : pouvoir utiliser les champs liés dans les boucles...
	 *
	 * 1. liaisons simples entre auteurs et contacts par le id_auteur
	 * 2. liaison complexe entre auteurs et adresses par le id_adresse -> id_contact -> id_auteur
	 */
	$interface['tables_jointures']['spip_contacts'][]= 'auteurs';
	$interface['tables_jointures']['spip_auteurs'][]= 'contacts';

	$interface['tables_jointures']['spip_comptes'][]= 'contacts';
	$interface['tables_jointures']['spip_contacts'][]= 'comptes';

	$interface['tables_jointures']['spip_adresses'][] = 'adresses_liens';
	$interface['tables_jointures']['spip_contacts'][] = 'adresses_liens';
	$interface['tables_jointures']['spip_adresses_liens'][] = 'adresses';

	$interface['tables_jointures']['spip_numeros'][] = 'numeros_liens';
	$interface['tables_jointures']['spip_contacts']['id_objet'] = 'numeros_liens';
	$interface['tables_jointures']['spip_numeros_liens'][] = 'numeros';
	
	$interface['tables_jointures']['spip_champs'][] = 'champs_liens';
	$interface['tables_jointures']['spip_contacts']['id_objet'] = 'champs_liens';
	$interface['tables_jointures']['spip_champs_liens'][] = 'champs';

	$interface['exceptions_des_jointures']['prenom'] = array('spip_contacts', 'prenom');
	$interface['exceptions_des_jointures']['id_contact'] = array('spip_contacts', 'id_contact');
		
	/**
	 * Objectif : autoriser les traitements SPIP sur certains champs texte...
	 *
	 */
	$interface['table_des_traitements']['NOM'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['PRENOM'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['CIVILITE'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['VILLE'][] = _TRAITEMENT_TYPO;

	return $interface;
}


function comptes_declarer_tables_principales($tables_principales){
	//-- Table contacts ------------------------------------------
	$contacts = array(
		"id_contact" 	=> "bigint(21) NOT NULL auto_increment",
		"id_auteur" 	=> "bigint(21) NOT NULL DEFAULT 0",
		"id_compte" 	=> "bigint(21) NOT NULL DEFAULT 0",
		"civilite" 		=> "tinytext DEFAULT '' NOT NULL",
		"prenom"		=> "VARCHAR(10) NOT NULL DEFAULT 0",
		"nom" 			=> "tinytext DEFAULT '' NOT NULL",
		"naissance"		=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"descriptif" 	=> "tinytext DEFAULT '' NOT NULL",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"maj"			=> "TIMESTAMP"
		);
	$contacts_key = array(
		"PRIMARY KEY"	=> "id_contact",
		"KEY id_auteur" => "id_auteur"
		);
	$tables_principales['spip_contacts'] =
		array('field' => &$contacts, 'key' => &$contacts_key, 'join' => &$contacts_join);


	//-- Table comptes ------------------------------------------
	$comptes = array(
		"id_compte" 	=> "bigint(21) NOT NULL auto_increment",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif"	=> "tinytext DEFAULT '' NOT NULL",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"maj"			=> "TIMESTAMP"
		);
	$comptes_key = array(
		"PRIMARY KEY"	=> "id_compte"
		);
	$tables_principales['spip_comptes'] =
		array('field' => &$comptes, 'key' => &$comptes_key);




	//-- Table adresses ------------------------------------------
	$adresses = array(
		"id_adresse"	=> "bigint(21) NOT NULL auto_increment",
		"type_adresse"	=> "VARCHAR(10) DEFAULT '' NOT NULL", // perso, pro, vacance...
		"numero" 		=> "VARCHAR(10) DEFAULT '' NOT NULL", // p. ex. 21
		"voie"			=> "tinytext DEFAULT '' NOT NULL", // p. ex. rue de cotte
		"complement"	=> "tinytext DEFAULT '' NOT NULL", // p. ex. 3è étage
		"boite_postale"	=> "VARCHAR(10) DEFAULT '' NOT NULL", 
		"code_postal"	=> "VARCHAR(5) DEFAULT '' NOT NULL",
		"ville"			=> "tinytext DEFAULT '' NOT NULL",
		"pays"			=> "tinytext DEFAULT '' NOT NULL",
		"maj"			=> "TIMESTAMP"
		);
	$adresses_key = array(
		"PRIMARY KEY"	=> "id_adresse"
		);
	$tables_principales['spip_adresses'] =
		array(
			'field' => &$adresses, 'key' => &$adresses_key, 'join' => &$adresses_join);
		
		
		

	//-- Table numeros ------------------------------------------
	$numeros = array(
		"id_numero"		=> "bigint(21) NOT NULL auto_increment",
		"type_numero"	=> "VARCHAR(10) DEFAULT '' NOT NULL", // peut etre domicile, bureau, portable
		"numero" 		=> "tinytext DEFAULT '' NOT NULL",
		"maj"			=> "TIMESTAMP"
		);
	$numeros_key = array(
		"PRIMARY KEY"	=> "id_numero"
		);
	$tables_principales['spip_numeros'] =
		array('field' => &$numeros, 'key' => &$numeros_key, 'join' => &$numeros_join);




	//-- Table champs ------------------------------------------
	$champs = array(
		"id_champ"		=> "bigint(21) NOT NULL auto_increment",
		"type_champ"	=> "VARCHAR(10) DEFAULT '' NOT NULL", // peut etre domicile, bureau, portable
		"descriptif"	=> "tinytext DEFAULT '' NOT NULL",
		"maj"			=> "TIMESTAMP"
		);
	$champs_key = array(
		"PRIMARY KEY"	=> "id_champ"
		);
	$tables_principales['spip_champs'] =
		array('field' => &$champs, 'key' => &$champs_key, 'join' => &$champs_join);





	return $tables_principales;

}


function comptes_declarer_tables_auxiliaires($tables_auxiliaires){
	//-- Table adresses_liens ---------------------------------------
	$adresses_liens = array(
		"id_adresse"	=> "BIGINT(21) NOT NULL",
		"id_objet"		=> "BIGINT(21) NOT NULL",
		"objet"			=> "varchar(25) NOT NULL" // peut etre un compte ou un contact
	);
	$adresses_liens_key = array(
		"PRIMARY KEY"	=> "id_adresse, id_objet, objet",
		"KEY"			=> "id_adresse"
		);
	$tables_auxiliaires['spip_adresses_liens'] =
		array('field' => &$adresses_liens, 'key' => &$adresses_liens_key);


	//-- Table numeros_liens ------------------------------------------
	$numeros_liens = array(
		"id_numero"		=> "bigint(21) NOT NULL DEFAULT 0",
		"id_objet"		=> "bigint(21) NOT NULL DEFAULT 0", 
		"objet"			=> "varchar(25) NOT NULL" // peut etre un contact ou un compte
		);
	$numeros_liens_key = array(
		"PRIMARY KEY"	=> "id_numero, id_objet, objet",
		"KEY"			=> "id_numero"
		);
	$tables_auxiliaires['spip_numeros_liens'] =
		array('field' => &$numeros_liens, 'key' => &$numeros_liens_key);



	//-- Table champs_liens ------------------------------------------
	$champs_liens = array(
		"id_champ"		=> "bigint(21) NOT NULL DEFAULT 0",
		"id_objet"		=> "bigint(21) NOT NULL DEFAULT 0", 
		"objet"			=> "varchar(25) NOT NULL" // peut etre un contact ou un compte ou n'importe quoi
		);
	$champs_liens_key = array(
		"PRIMARY KEY"	=> "id_champ, id_objet, objet",
		"KEY"			=> "id_champ"
		);
	$tables_auxiliaires['spip_champs_liens'] =
		array('field' => &$champs_liens, 'key' => &$champs_liens_key);
	



	return $tables_auxiliaires;
}

?>