<?php
/**
 * Plugin Comptes pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */


function comptes_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['comptes'] = 'comptes';
	$interface['table_des_tables']['contacts'] = 'contacts';
	$interface['table_des_tables']['liens'] = 'liens';
	
	/**
	 * Objectif : pouvoir utiliser les champs liés dans les boucles...
	 *
	 */
	$interface['tables_jointures']['spip_auteurs']['id_auteur']= 'contacts';
	$interface['tables_jointures']['spip_contacts']['id_auteur']= 'auteurs';
	$interface['tables_jointures']['spip_comptes']['id_compte']= 'contacts';
	$interface['tables_jointures']['spip_contacts']['id_compte']= 'comptes';

	
	/**
	 * Objectif : autoriser les traitements SPIP sur certains champs texte...
	 *
	 */
	$interface['table_des_traitements']['NOM'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['PRENOM'][] = _TRAITEMENT_TYPO;

	return $interface;
}


function comptes_declarer_tables_principales($tables_principales){
	//-- Table contacts ------------------------------------------
	$contacts = array(
		"id_contact" 	=> "bigint(21) NOT NULL auto_increment",
		"id_auteur" 	=> "bigint(21) NOT NULL DEFAULT 0",
		"id_compte" 	=> "bigint(21) NOT NULL DEFAULT 0",
		"civilite" 		=> "tinytext DEFAULT '' NOT NULL",
		"nom" 			=> "tinytext DEFAULT '' NOT NULL",
		"prenom"		=> "VARCHAR(10) NOT NULL DEFAULT 0",
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
		array('field' => &$contacts, 'key' => &$contacts_key);


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


	//-- Table coordonnees ------------------------------------------
	$coordonnees = array(
		"id_coordonnee"	=> "bigint(21) NOT NULL auto_increment",
		"type"			=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif"	=> "tinytext DEFAULT '' NOT NULL",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"maj"			=> "TIMESTAMP"
		);
	$coordonnees_key = array(
		"PRIMARY KEY"	=> "id_coordonnee"
		);
	$tables_principales['spip_coordonnees'] =
		array('field' => &$coordonnees, 'key' => &$coordonnees_key);


	//-- Table coordonnees_liens ------------------------------------------
	$coordonnees_liens = array(
		"id_coordonnee"	=> "bigint(21) NOT NULL DEFAULT 0",
		"id_objet"		=> "bigint(21) NOT NULL DEFAULT 0", 
		"objet"			=> "tinytext DEFAULT '' NOT NULL" // peut etre un contact ou un compte
		);
	$coordonnees_liens_key = array(
		"PRIMARY KEY"	=> "id_coordonnee, id_objet"
		);
	$tables_principales['spip_coordonnees_liens'] =
		array('field' => &$coordonnees_liens, 'key' => &$coordonnees_liens_key);


	return $tables_principales;

}


?>