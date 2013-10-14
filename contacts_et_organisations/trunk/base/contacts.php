<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Pipelines
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer les interfaces des tables organisations et contacts
 *
 * @pipeline declarer_tables_interfaces
 * 
 * @param array $interface
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function contacts_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['annuaires'] = 'annuaires';
	$interface['table_des_tables']['organisations'] = 'organisations';
	$interface['table_des_tables']['organisations_liens'] = 'organisations_liens';
	$interface['table_des_tables']['contacts'] = 'contacts';
	$interface['table_des_tables']['contacts_liens'] = 'contacts_liens';
	$interface['table_des_tables']['organisations_contacts'] = 'organisations_contacts';
	
	// -- Liaisons organisations/contacts
	$interface['tables_jointures']['spip_organisations_contacts'][]= 'contacts';
	$interface['tables_jointures']['spip_organisations_contacts'][]= 'organisations';

	/**
	 * Objectif : autoriser les traitements SPIP sur certains champs texte...
	 */
	$interface['table_des_traitements']['NOM'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['PRENOM'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['CIVILITE'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['FONCTION'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['ACTIVITE'][] = _TRAITEMENT_TYPO;
	
	// Chercher plus facilement dans les annuaires avec {annuaire=truc}
	$interface['exceptions_des_tables']['organisations']['annuaire'] = array('spip_annuaires', 'identifiant');
	$interface['exceptions_des_tables']['contacts']['annuaire'] = array('spip_annuaires', 'identifiant');
	
	return $interface;
}


/**
 * Déclarer les objets éditoriaux des contacts et organisations
 *
 * @pipeline declarer_tables_objets_sql
 * 
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function contacts_declarer_tables_objets_sql($tables){
	//-- Table annuaires ----------------------------------------
	$tables['spip_annuaires'] = array(
		// Caractéristiques
		'principale' => 'oui',
		'page'=>'annuaire',
		// Les champs et leurs particularités (clés etc)
		'field'=> array(
			'id_annuaire' 		=> "bigint(21) NOT NULL auto_increment",
			'identifiant'		=> 'varchar(255) not null default ""',
			'titre' 			=> "text DEFAULT '' NOT NULL",
			'descriptif'		=> "TEXT DEFAULT '' NOT NULL",
			'maj'				=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"		=> "id_annuaire",
			'KEY identifiant'	=> 'identifiant',
		),
		'join' => array(
			"id_annuaire" 	=> "id_annuaire",
		),
		'titre' => 'titre, "" AS lang',
		'champs_editables' => array('identifiant', 'titre', 'descriptif'),
		'champs_versionnes' => array('identifiant', 'titre', 'descriptif'),
		'rechercher_champs' => array(
			'identifiant' => 8, 'titre' => 8, 'descriptif' => 4,
		),
		'tables_jointures' => array(
			'contacts',
			'organisations',
		),
		// Chaînes de langue explicite
		'texte_objets' => 'contacts:annuaires',
		'texte_objet' => 'contacts:annuaire',
		'texte_modifier' => 'contacts:annuaire_editer',
		'texte_creer' => 'contacts:annuaire_creer',
		'texte_creer_associer' => 'contacts:annuaire_creer_associer',
		'texte_ajouter' => 'contacts:annuaire_ajouter',
		'texte_logo_objet' => 'contacts:annuaire_logo',
		'info_aucun_objet'=> 'contacts:annuaire_aucun',
		'info_1_objet' => 'contacts:annuaire_un',
		'info_nb_objets' => 'contacts:annuaires_nb',
	);
	
	//-- Table organisations ----------------------------------------
	$tables['spip_organisations'] = array(
		'page'=>'organisation',
		'texte_objets' => 'contacts:organisations',
		'texte_objet' => 'contacts:organisation',
		'texte_modifier' => 'contacts:organisation_editer',
		'texte_creer' => 'contacts:organisation_creer',
		'texte_creer_enfant' => 'contacts:organisation_creer_fille',
		'texte_creer_associer' => 'contacts:organisation_creer_associer',
		'texte_ajouter' => 'contacts:organisation_ajouter',
		'texte_logo_objet' => 'contacts:organisation_logo',
		'info_aucun_objet'=> 'contacts:organisation_aucun',
		'info_1_objet' => 'contacts:organisation_un',
		'info_nb_objets' => 'contacts:organisations_nb',
		'titre' => 'nom AS titre, "" AS lang',
		'date' => 'date_creation',
		'principale' => 'oui',
		'champs_editables' => array(
			'id_parent', 'id_auteur', 'id_annuaire',
			'nom', 'statut_juridique', 'identification', 'activite',
			'date_creation', 'descriptif'),
		'field'=> array(
			"id_organisation" 	=> "bigint(21) NOT NULL auto_increment",
			'id_annuaire'		=> 'bigint(21) NOT NULL default 0',
			"id_parent"			=> "bigint(21) NOT NULL default 0",
			"id_auteur"   		=> "bigint(21) NOT NULL default 0",
			"nom" 				=> "tinytext DEFAULT '' NOT NULL",
			"statut_juridique"	=> "tinytext DEFAULT '' NOT NULL", // forme juridique : SA, SARL, association, etc.
			"identification"	=> "tinytext DEFAULT '' NOT NULL", // N° d'identification : SIRET, SIREN, N° TVA...
			"activite"			=> "tinytext DEFAULT '' NOT NULL", // Secteur d'activité : humanitaire, formation...
			"date_creation"		=> "datetime NOT NULL default '0000-00-00 00:00:00'",
			"descriptif"		=> "TEXT DEFAULT '' NOT NULL",
			"maj"				=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"		=> "id_organisation",
			"KEY id_auteur"		=> "id_auteur",
			'KEY id_annuaire'	=> 'id_annuaire',
		),
		'join' => array(
			"id_organisation" 	=> "id_organisation",
			"id_auteur" 		=> "id_auteur",
			'id_annuaire'		=> 'id_annuaire',
		),
		'tables_jointures' => array(
			'auteurs', 'annuaires',
			'organisations_contacts',
			'organisations_liens',
		),
		'rechercher_champs' => array(
			'id_organisation' => 12, 'nom' => 8,
		),
		/*
		'rechercher_jointures' => array(
			'auteur' => array('nom' => 2, 'bio' => 1)
		),*/
		'champs_versionnes' => array(
			'id_parent', 'id_auteur', 'id_annuaire',
			 'nom', 'descriptif', 'identification', 'statut_juridique',
			 'activite', 'date_creation'),
	);



	//-- Table contacts ----------------------------------------
	$tables['spip_contacts'] = array(
		'page'=>'contact',
		'texte_objets' => 'contacts:contacts',
		'texte_objet' => 'contacts:contact',
		'texte_modifier' => 'contacts:contact_editer',
		'texte_creer' => 'contacts:contact_creer',
		'texte_creer_associer' => 'contacts:contact_creer_associer',
		'texte_ajouter' => 'contacts:contact_ajouter',
		'texte_logo_objet' => 'contacts:contact_logo',
		'info_aucun_objet'=> 'contacts:contact_aucun',
		'info_1_objet' => 'contacts:contact_un',
		'info_nb_objets' => 'contacts:contacts_nb',
		'titre' => 'nom AS titre, "" AS lang',
		'date' => 'date_naissance',
		'principale' => 'oui',
		'champs_editables' => array(
			'id_auteur', 'id_annuaire', 'civilite', 'nom', 'prenom', 'fonction', 
			'date_naissance', 'descriptif'),
		'field'=> array(
			"id_contact"	=> "bigint(21) NOT NULL auto_increment",
			'id_annuaire'		=> 'bigint(21) NOT NULL default 0',
			"id_auteur"   	=> "bigint(21) NOT NULL default 0",
			"civilite" 		=> "tinytext DEFAULT '' NOT NULL",
			"nom" 			=> "tinytext DEFAULT '' NOT NULL",
			"prenom"		=> "tinytext NOT NULL DEFAULT ''",
			"fonction"		=> "tinytext DEFAULT '' NOT NULL", // gérant, membre, trésorier, etc.
			"date_naissance"=> "datetime NOT NULL default '0000-00-00 00:00:00'",
			"descriptif" 	=> "text DEFAULT '' NOT NULL",
			"maj"			=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"		=> "id_contact",
			"KEY id_auteur"		=> "id_auteur",
			'KEY id_annuaire'	=> 'id_annuaire',
		),
		'join' => array(
			"id_contact" 	=> "id_contact",
			"id_auteur" 	=> "id_auteur",
			'id_annuaire'	=> 'id_annuaire',
		),
		'tables_jointures' => array(
			'auteurs', 'annuaires',
			'organisations_contacts',
			'contacts_liens',
		),
		'rechercher_champs' => array(
			'id_contact' => 12, 'nom' => 8, 'prenom' => 2,
		),
		/*
		'rechercher_jointures' => array(
			'auteur' => array('nom' => 2, 'bio' => 1)
		),*/
		'champs_versionnes' => array(
			'id_auteur', 'id_annuaire', 'civilite', 'nom', 'prenom', 'fonction', 
			'date_naissance', 'descriptif'),
	);

	//-- Jointures ----------------------------------------------------
	$tables['spip_auteurs']['tables_jointures'][] = 'contacts';
	$tables['spip_auteurs']['tables_jointures'][] = 'organisations';

	return $tables;
}



/**
 * Déclarer les tables auxiliaires des contacts et organisations
 *
 * @pipeline declarer_tables_auxiliaires
 * 
 * @param array $tables_auxiliaires
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function contacts_declarer_tables_auxiliaires($tables_auxiliaires){

	//-- Table organisations_contacts -------------------------------------
	$organisations_contacts = array(
		"id_organisation" => "BIGINT(21) NOT NULL",
		"id_contact"      => "BIGINT(21) NOT NULL",
		"type_liaison"    => "tinytext NOT NULL DEFAULT ''",
	);
	$organisations_contacts_key = array(
		"PRIMARY KEY"          => "id_organisation, id_contact",
		"KEY id_organisation"  => "id_organisation",
		"KEY id_contact"       => "id_contact"
	);
	$tables_auxiliaires['spip_organisations_contacts'] =
		array('field' => &$organisations_contacts, 'key' => &$organisations_contacts_key);


	//-- Table organisations_liens -------------------------------------
	$organisations_liens = array(
		"id_organisation" => "BIGINT(21) NOT NULL",
		"id_objet"        => "BIGINT(21) NOT NULL",
		"objet"           => "VARCHAR(25) NOT NULL",
		"type_liaison"    => "VARCHAR(25) NOT NULL DEFAULT ''",
	);
	$organisations_liens_key = array(
		"PRIMARY KEY"         => "id_organisation, id_objet, objet, type_liaison",
		"KEY id_organisation" => "id_organisation"
	);
	$tables_auxiliaires['spip_organisations_liens'] =
		array('field' => &$organisations_liens, 'key' => &$organisations_liens_key);


	//-- Table contacts_liens -------------------------------------
	$contacts_liens = array(
		"id_contact"   => "BIGINT(21) NOT NULL",
		"id_objet"     => "BIGINT(21) NOT NULL",
		"objet"        => "VARCHAR(25) NOT NULL",
		"type_liaison" => "VARCHAR(25) NOT NULL DEFAULT ''",
	);
	$contacts_liens_key = array(
		"PRIMARY KEY"    => "id_contact, id_objet, objet, type_liaison",
		"KEY id_contact" => "id_contact"
	);
	$tables_auxiliaires['spip_contacts_liens'] =
		array('field' => &$contacts_liens, 'key' => &$contacts_liens_key);
	
	return $tables_auxiliaires;
}

?>
