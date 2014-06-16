<?php
/**
 * Plugin Contacts & Organisations pour Spip 2.0
 * Auteurs : Cyril Marion, Matthieu Marcillaud
 * Licence GPL (c) 2009 - 2011- Ateliers CYM
 */

function contacts_declarer_tables_interfaces($interface){	
	$interface['table_des_tables']['organisations'] = 'organisations';
	$interface['table_des_tables']['organisations_liens'] = 'organisations_liens';
	$interface['table_des_tables']['contacts'] = 'contacts';
	$interface['table_des_tables']['contacts_liens'] = 'contacts_liens';
	$interface['table_des_tables']['organisations_contacts'] = 'organisations_contacts';
	
	// -- Liaisons organisations/auteurs, contacts/auteurs et organisations/contacts
	$interface['tables_jointures']['spip_contacts'][]= 'auteurs';
	$interface['tables_jointures']['spip_contacts'][]= 'contacts_liens';
	$interface['tables_jointures']['spip_auteurs'][] = 'contacts';
	$interface['tables_jointures']['spip_organisations'][] = 'auteurs';
	$interface['tables_jointures']['spip_organisations'][] = 'organisations_liens';
	$interface['tables_jointures']['spip_auteurs'][] = 'organisations';
	$interface['tables_jointures']['spip_organisations_contacts'][]= 'contacts';
	$interface['tables_jointures']['spip_organisations_contacts'][]= 'organisations';
	$interface['tables_jointures']['spip_contacts'][]= 'organisations_contacts';
	$interface['tables_jointures']['spip_organisations'][]= 'organisations_contacts';


	/**
	 * Objectif : autoriser les traitements SPIP sur certains champs texte...
	 *
	 */
	$interface['table_des_traitements']['NOM'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['PRENOM'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['CIVILITE'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['FONCTION'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['ACTIVITE'][] = _TRAITEMENT_TYPO;

	// gerer le critere de date
	$interface['table_date']['contacts'] = 'date_naissance';
	$interface['table_date']['organisations'] = 'date_creation';

	// titre
	$interface['table_titre']['contacts'] = "nom AS titre, '' AS lang"; // pour avoir une #URL_CONTACT...
	$interface['table_titre']['organisations'] = "nom AS titre, '' AS lang";
	
	return $interface;
}


function contacts_declarer_tables_principales($tables_principales){
	//-- Table organisations ------------------------------------------
	$organisations = array(
		"id_organisation" 	=> "bigint(21) NOT NULL auto_increment",
		"id_parent"			=> "bigint(21) NOT NULL default 0",
		"id_auteur"   		=> "bigint(21) NOT NULL default 0",
		"nom" 				=> "tinytext DEFAULT '' NOT NULL",
        "statut_juridique"	=> "tinytext DEFAULT '' NOT NULL", // forme juridique : SA, SARL, association, etc.
        "identification"	=> "tinytext DEFAULT '' NOT NULL", // N° d'identification : SIRET, SIREN, N° TVA...
		"activite"			=> "tinytext DEFAULT '' NOT NULL", // Secteur d'activité : humanitaire, formation...
		"date_creation"		=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"descriptif"		=> "TEXT DEFAULT '' NOT NULL",
		"maj"				=> "TIMESTAMP"
		);
	$organisations_key = array(
		"PRIMARY KEY"		=> "id_organisation",
		"KEY id_auteur"		=> "id_auteur",
		);
	$organisations_join = array(
		// sinon (ORGANISATIONS){auteurs.statut = xxx} ne fonctionne pas...
		// va comprendre...
		"id_organisation" 	=> "id_organisation",
		"id_auteur" 	=> "id_auteur"
	);
	$tables_principales['spip_organisations'] =
		array('field' => &$organisations, 'key' => &$organisations_key, 'join' => &$organisations_join);

	//-- Table contacts ------------------------------------------
	$contacts = array(
		"id_contact"	=> "bigint(21) NOT NULL auto_increment",
		"id_auteur"   	=> "bigint(21) NOT NULL default 0",
		"civilite" 		=> "tinytext DEFAULT '' NOT NULL",
		"nom" 			=> "tinytext DEFAULT '' NOT NULL",
		"prenom"		=> "tinytext NOT NULL DEFAULT ''",
		"fonction"		=> "tinytext DEFAULT '' NOT NULL", // gérant, membre, trésorier, etc.
		"date_naissance"=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"descriptif" 	=> "text DEFAULT '' NOT NULL",
		"maj"			=> "TIMESTAMP"
		);
	$contacts_key = array(
		"PRIMARY KEY"	=> "id_contact",
		"KEY id_auteur"	=> "id_auteur",
		);
	$contacts_join = array(
		"id_contact" => "id_contact",
		"id_auteur"  => "id_auteur"
	);
	$tables_principales['spip_contacts'] =
		array('field' => &$contacts, 'key' => &$contacts_key, 'join' => &$contacts_join);

	return $tables_principales;

}


function contacts_declarer_tables_auxiliaires($tables_auxiliaires){

    //-- Table organisations_contacts -------------------------------------
    $organisations_contacts = array(
        "id_organisation" => "BIGINT(21) NOT NULL",
        "id_contact"      => "BIGINT(21) NOT NULL",
        "type_liaison"    => "tinytext NOT NULL DEFAULT ''",
    );
    $organisations_contacts_key = array(
        "PRIMARY KEY"	       => "id_organisation, id_contact",
		"KEY id_organisation"  => "id_organisation",
		"KEY id_contact"       => "id_contact"
    );
	$tables_auxiliaires['spip_organisations_contacts'] =
		array('field' => &$organisations_contacts, 'key' => &$organisations_contacts_key);


    //-- Table organisations_liens -------------------------------------
    $organisations_liens = array(
        "id_organisation" => "BIGINT(21) NOT NULL",
        "id_objet"   	=> "BIGINT(21) NOT NULL",
        "objet"      	=> "VARCHAR(25) NOT NULL",
        "type_liaison"    => "tinytext NOT NULL DEFAULT ''",
    );
    $organisations_liens_key = array(
        "PRIMARY KEY"    => "id_organisation, id_objet, objet, type_liaison (25)",
		"KEY id_organisation" => "id_organisation",
		"KEY id_objet" => "id_objet",
		"KEY objet" => "objet"
    );
	$tables_auxiliaires['spip_organisations_liens'] =
		array('field' => &$organisations_liens, 'key' => &$organisations_liens_key);


    //-- Table contacts_liens -------------------------------------
    $contacts_liens = array(
        "id_contact" => "BIGINT(21) NOT NULL",
        "id_objet"   => "BIGINT(21) NOT NULL",
        "objet"      => "VARCHAR(25) NOT NULL",
        "type_liaison"    => "tinytext NOT NULL DEFAULT ''",
    );
    $contacts_liens_key = array(
        "PRIMARY KEY"    => "id_contact, id_objet, objet, type_liaison (25)",
		"KEY id_contact" => "id_contact",
		"KEY id_objet" => "id_objet",
		"KEY objet" => "objet"
    );
	$tables_auxiliaires['spip_contacts_liens'] =
		array('field' => &$contacts_liens, 'key' => &$contacts_liens_key);
	
	return $tables_auxiliaires;
}

?>
