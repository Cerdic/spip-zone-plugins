<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */

function contacts_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['organisations'] = 'organisations';
	$interface['table_des_tables']['contacts'] = 'contacts';
	$interface['table_des_tables']['organisations_contacts'] = 'organisations_contacts';
	
	// -- Liaisons comptes/auteurs, contacts/auteurs et comptes/contacts
	$interface['tables_jointures']['spip_contacts'][]= 'auteurs';
	$interface['tables_jointures']['spip_auteurs'][]= 'contacts';
	$interface['tables_jointures']['spip_organisations'][]= 'auteurs';
	$interface['tables_jointures']['spip_auteurs'][]= 'organisations';
	$interface['tables_jointures']['spip_organisations_contacts'][]= 'contacts';
	$interface['tables_jointures']['spip_organisations_contacts'][]= 'organsiations';
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
	$interface['table_titre']['contacts'] = "CONCAT(nom,' ',prenom) AS titre, '' AS lang";
	$interface['table_titre']['organisations'] = "nom AS titre, '' AS lang";
	
	return $interface;
}


function contacts_declarer_tables_principales($tables_principales){
	//-- Table organisations ------------------------------------------
	$organisations = array(
		"id_organisation" => "bigint(21) NOT NULL auto_increment",
		"id_auteur"		=> "bigint(21) NOT NULL",
		"nom" 			=> "tinytext DEFAULT '' NOT NULL",
        "statut_juridique"	=> "tinytext DEFAULT '' NOT NULL", // forme juridique : SA, SARL, association, etc.
        "identification"	=> "tinytext DEFAULT '' NOT NULL", // N° d'identification : SIRET, SIREN, N° TVA...
		"activite"		=> "tinytext DEFAULT '' NOT NULL", // Secteur d'activité : humanitaire, formation...
		"date_creation"	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"descriptif"	=> "tinytext DEFAULT '' NOT NULL",
		"maj"			=> "TIMESTAMP"
		);
	$organisations_key = array(
		"PRIMARY KEY"	=> "id_organisation",
		"KEY id_auteur" => "id_auteur"
		);
	$organisations_join = array(
		"id_auteur" => "id_auteur",
		"id_organisation" => "id_organisation"
	);
	$tables_principales['spip_organisations'] =
		array('field' => &$organisations, 'key' => &$organisations_key, 'join' => &$organisations_join);

	//-- Table contacts ------------------------------------------
	$contacts = array(
		"id_contact"	=> "bigint(21) NOT NULL auto_increment", 
		"id_auteur"		=> "bigint(21) NOT NULL",
		"civilite" 		=> "tinytext DEFAULT '' NOT NULL",
		"nom" 			=> "tinytext DEFAULT '' NOT NULL",
		"prenom"		=> "tinytext NOT NULL DEFAULT ''",
		"fonction"		=> "tinytext DEFAULT '' NOT NULL", // gérant, membre, trésorier, etc.
		"date_naissance"=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"descriptif" 	=> "tinytext DEFAULT '' NOT NULL",
		"maj"			=> "TIMESTAMP"
		);
	$contacts_key = array(
		"PRIMARY KEY"	=> "id_contact",
		"KEY id_auteur" => "id_auteur"
		);
	$contacts_join = array(
		"id_auteur" => "id_auteur",
		"id_contact" => "id_contact"
	);
	$tables_principales['spip_contacts'] =
		array('field' => &$contacts, 'key' => &$contacts_key, 'join' => &$contacts_join);

	return $tables_principales;

}


function contacts_declarer_tables_auxiliaires($tables_auxiliaires){

    //-- Table organisations_contacts -------------------------------------
    $organisations_contacts = array(
        "id_organisation"     => "BIGINT(21) NOT NULL",
        "id_contact"    => "BIGINT(21) NOT NULL"
    );
    $organisations_contacts_key = array(
        "PRIMARY KEY"	=> "id_organisation, id_contact",
		"KEY id_organisation"	=> "id_organisation",
		"KEY id_contact"	=> "id_contact"
    );
	$tables_auxiliaires['spip_organisations_contacts'] =
		array('field' => &$organisations_contacts, 'key' => &$organisations_contacts_key);
	
	return $tables_auxiliaires;
}

?>
