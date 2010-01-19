<?php
/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - 2010
 * Auteur Cyril MARION - Ateliers CYM
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function spipmine_declarer_tables_principales($tables_principales){

	// -- Table actions ----------------------------------------------
	$spipmine_actions = array(
		"id_action"				=>	"int(11) NOT NULL auto_increment",
		"id_projet"				=>	"int(11) default NULL",
		"id_forfait"			=>	"int(11) default NULL",
		"id_type_action"		=>	"int(11) default NULL",
		"id_facture"			=>	"int(11) default NULL",
		"date_action"			=>	"DATETIME NULL default NULL",
		"action"				=>	"varchar(255) default NULL",
		"type_action"			=>	"varchar(50) default NULL",
		"id_user"				=>	"varchar(50) default NULL",
		"heure_debut"			=>	"varchar(40) default NULL",
		"heure_fin"				=>	"varchar(40) default NULL",
		"nb_heures_passees"		=>	"decimal(18,2) default NULL",
		"nb_heures_decomptees"	=>	"decimal(18,2) default NULL"
	);
	$spipmine_actions_key = array(
		"PRIMARY KEY"			=>	"id_action",
		"KEY id_projet"			=>	"id_projet",
		"KEY id_forfait"		=>	"id_forfait",
		"KEY type_action"		=>	"type_action",
		"KEY id_type_action"	=>	"id_type_action"
	);
	$tables_principales['spipmine_actions'] = array(
		"field" => &$spipmine_actions,
		"key" => &$spipmine_actions_key
	);

	
	// -- Table factures ----------------------------------------------
	$spipmine_factures = array(
		"id_facture"			=>	"int(11) NOT NULL auto_increment",
		"num_facture"			=>	"varchar(50) default NULL",
		"num_devis"				=>	"varchar(50) default NULL",
		"id_type_document"		=>	"int(11) default NULL",
		"proforma"				=>	"tinyint(1) default NULL",
		"delais_validite"		=>	"int(11) default NULL",
		"date_facture"			=>	"DATETIME NULL default NULL",
		"date_payement"			=>	"DATETIME NULL NULL",
		"reglement"				=>	"varchar(50) default NULL",
		"fin_validite"			=>	"DATETIME NULL default NULL",
		"id_client"				=>	"int(11) default NULL",
		"id_projet"				=>	"smallint(6) default NULL",
		"nom_client"			=>	"varchar(255) default NULL",
		"id_type_presta"		=>	"int(11) default NULL",
		"montant"				=>	"decimal(18,2) default NULL",
		"charge_estimee"		=>	"float default NULL",
		"delais"				=>	"varchar(50) default NULL",
		"nb_heures_vendues"		=>	"decimal(18,2) default NULL",
		"libelle_facture"		=>	"mediumtext",
		"libelle_forfait"		=>	"mediumtext",
		"nota_bene"				=>	"mediumtext"
	);
	$spipmine_factures_key = array(
		"PRIMARY KEY"			=>	"id_facture",
		"KEY id_projet"			=>	"id_projet",
		"KEY id_client"			=>	"id_client",
		"KEY date_facture"		=>	"date_facture",
		"KEY num_facture"		=>	"num_facture"
	);
	$tables_principales['spipmine_factures'] = array(
		"field" => &$spipmine_factures,
		"key" => &$spipmine_factures_key
	);
	
	
	// -- Table projets ----------------------------------------------
	$spipmine_projets = array(
		"id_projet"					=>	"int(11) NOT NULL auto_increment",
		"id_parent"					=>	"int(11) default NULL",
		"nom_projet"				=>	"varchar(75) default NULL",
		"url_projet"				=>	"varchar(255) default NULL",
		"id_client"					=>	"int(11) default NULL",
		"id_type_facturation"		=>	"int(11) default NULL",
		"id_type_presta"			=>	"int(11) default NULL",
		"id_type_status"			=>	"int(11) default NULL",
		"num_devis"					=>	"varchar(50) default NULL",
		"num_maquette"				=>	"int(9) default NULL",
		"num_cdc"					=>	"int(9) default NULL",
		"num_benchmark"				=>	"int(9) default NULL",
		"num_doc"					=>	"int(9) default NULL",
		"num_dev"					=>	"int(9) default NULL",
		"date_saisie"				=>	"DATETIME NULL default NULL",
		"date_maj"					=>	"TIMESTAMP NULL default CURRENT_TIMESTAMP",
		"date_livraison_prevue"		=>	"DATETIME NULL default NULL",
		"date_debut"				=>	"DATETIME NULL default NULL",
		"date_livraison"			=>	"DATETIME NULL default NULL",
		"delais"					=>	"int(11) default NULL",
		"budget"					=>	"int(11) default NULL",
		"nb_heures_estimees"		=>	"decimal(18,2) default NULL",
		"nb_heures_facturees"		=>	"decimal(18,2) default NULL",
		"actif"						=>	"enum('non','oui') NOT NULL default 'oui'",
		"facture"					=>	"enum('non','oui') NOT NULL default 'non'",
		"objectif"					=>	"mediumtext",
		"enjeux"					=>	"mediumtext",
		"methode"					=>	"mediumtext",
		"commentaires"				=>	"mediumtext",
		"importance"				=>	"varchar(255) default NULL"
	);
	$spipmine_projets_key = array(
		"PRIMARY KEY"				=>	"id_projet",
		"KEY id_parent"				=>	"id_parent",
		"KEY nom_projet"			=>	"nom_projet",
		"KEY id_client"				=>	"id_client"
	);
	$tables_principales['spipmine_projets'] = array(
		"field" => &$spipmine_projets,
		"key" => &$spipmine_projets_key
	);


	// -- Table reglements ----------------------------------------------
	$spipmine_reglements = array(
		"id_reglement"		=> "int(11) NOT NULL auto_increment",
		"id_type_reglement"	=> "int(11) default NULL",
		"id_facture"		=> "int(11) NOT NULL default '0'",
		"date_reglement"	=> "datetime default NULL",
		"montant"			=> "decimal(18,2) default NULL",
		"commentaires"		=> "text"
	);
	$spipmine_reglements_key = array(
		"PRIMARY KEY"		=> "id_reglement"
	);
	$tables_principales['spipmine_reglements'] = array(
		"field" => &$spipmine_reglements,
		"key" => &$spipmine_reglements_key
	);

	/**
	 * Tables clients, contacts
	 * à remplacer des que possible par le plugin Comptes & Contacts
	 *
	 */
	// -- Table clients ----------------------------------------------
	$spipmine_clients = array(
		"id_client"				=>	"int(11) NOT NULL auto_increment",
		"nom_client"			=>	"varchar(125) default NULL",
		"nom_court"				=>	"varchar(8) default NULL",
		"adresse_1"				=>	"varchar(125) default NULL",
		"adresse_2"				=>	"varchar(125) default NULL",
		"boite_postale"			=>	"varchar(20) default NULL",
		"code_postal"			=>	"varchar(50) default NULL",
		"ville"					=>	"varchar(50) default NULL",
		"commentaires"			=>	"blob"
	);
	$spipmine_clients_key = array(
		"PRIMARY KEY"			=>	"id_client",
		"KEY nom_client"		=>	"nom_client"
	);
	$tables_principales['spipmine_clients'] = array(
		"field" => &$spipmine_clients,
		"key" => &$spipmine_clients_key
	);
	// -- Table contacts ----------------------------------------------
	$spipmine_contacts = array(
		"id_contact"			=>	"int(11) NOT NULL auto_increment",
		"id_compte"				=>	"int(11) default NULL",
		"titre"					=>	"varchar(50) default NULL",
		"prenom"				=>	"varchar(50) default NULL",
		"nom"					=>	"varchar(50) default NULL",
		"fonction"				=>	"varchar(50) default NULL",
		"telephone" 			=>	"varchar(25) default NULL",
		"email"					=>	"varchar(50) default NULL",
		"facture"				=>	"tinyint(1) default NULL",
		"commentaire"			=>	"mediumtext"
	);
	$spipmine_contacts_key = array(
		"PRIMARY KEY"			=>	"id_contact",
		"KEY id_compte"			=>	"id_compte"
	);
	$tables_principales['spipmine_contacts'] = array(
		"field" => &$spipmine_contacts,
		"key" => &$spipmine_contacts_key
	);
	/**
	 * Table users
	 * à remplacer des que possible par un lien vers spip_auteurs
	 *
	 */
	// -- Table users ----------------------------------------------
	$spipmine_users = array(
		"id_user"		=> "int(11) NOT NULL auto_increment",
		"nom_user"		=> "varchar(50) default NULL",
		"prenom_user"	=> "varchar(50) default NULL",
		"initiales"		=> "varchar(50) default NULL",
		"login"			=> "varchar(50) default NULL",
		"statut"		=> "enum('actif','non actif') default 'actif'"
	);
	$spipmine_users_key = array(
		"PRIMARY KEY"	=>	"id_user"
	);
	$tables_principales['spipmine_users'] = array(
		"field" => &$spipmine_users,
		"key" => &$spipmine_users_key
	);	
	
	return $tables_principales;
	
}

?>