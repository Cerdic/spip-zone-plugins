<?php 
/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - 2010
 * Auteur Cyril MARION - Ateliers CYM
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function spipmine_declarer_tables_auxiliaires($tables_auxiliaires){

	// structure de la table spipmine_clients_rubriques
	// permettant le lien entre les rubriques et les clients
	$spipmine_clients_rubriques = array(
		"id_rubrique"			=>	"int(11) NOT NULL",
		"id_client"				=>	"int(11) NOT NULL"
	);
	$spipmine_clients_rubriques_key = array(
		"PRIMARY KEY"			=>	"id_rubrique"
	);
	$tables_auxiliaires['spipmine_clients_rubriques'] = array(
		'field' => &$spipmine_clients_rubriques,
		'key' => &$spipmine_clients_rubriques_key
	);


	// structure de la table spipmine_lignes_factures
	// liée à la table spipmine_factures
	$spipmine_lignes_facture = array(
		"id_ligne"				=>	"int(11) NOT NULL auto_increment",
		"id_facture"			=>	"int(11) default NULL",
		"position"				=>	"int(11) default NULL",
		"quantite"				=>	"float default NULL",
		"unite"					=>	"varchar(50) default NULL",
		"designation"			=>	"text default NULL",
		"prix_unitaire_ht"		=>	"int(11) default NULL",
		"commentaire"			=>	"mediumtext"
	);
	$spipmine_lignes_facture_key = array(
		"PRIMARY KEY"			=>	"id_ligne",
		"KEY id_facture"		=>	"id_facture"
	);
	$tables_auxiliaires['spipmine_lignes_facture'] = array(
		'field' => &$spipmine_lignes_facture,
		'key' => &$spipmine_lignes_facture_key
	);



	// structure de la table spipmine_types_actions
	$spipmine_types_actions = array(
		"id_type_action"		=>	"int(11) NOT NULL auto_increment",
		"nom_type_action"		=>	"varchar(255) default NULL",
		"commentaires"			=>	"mediumtext"
	);
	$spipmine_types_actions_key = array(
		"PRIMARY KEY"			=>	"id_type_action"
	);
	$tables_auxiliaires['spipmine_types_actions'] = array(
		'field' => &$spipmine_types_actions,
		'key' => &$spipmine_types_actions_key
	);


	
	// structure de la table spipmine_types_documents
	$spipmine_types_documents = array(
		"id_type_document"		=>	"int(11) NOT NULL auto_increment",
		"nom_type_document"		=>	"varchar(50) default NULL"
	);
	$spipmine_types_documents_key = array(
		"PRIMARY KEY"			=>	"id_type_document"
	);
	$tables_auxiliaires['spipmine_types_documents'] = array(
		'field' => &$spipmine_types_documents,
		'key' => &$spipmine_types_documents_key
	);

	
	
	// structure de la table spipmine_types_livrables
	$spipmine_types_livrables = array(
		"id_type_livrable"		=>	"int(11) NOT NULL auto_increment",
		"nom_type_livrable"		=>	"varchar(50) default NULL"
	);
	$spipmine_types_livrables_key = array(
		"PRIMARY KEY"			=>	"id_type_livrable"
	);
	$tables_auxiliaires['spipmine_types_livrables'] = array(
		'field' => &$spipmine_types_livrables,
		'key' => &$spipmine_types_livrables_key
	);

	
	
	// structure de la table spipmine_types_prestations
	$spipmine_types_prestations = array(
		"id_type_presta"		=>	"int(11) NOT NULL auto_increment",
		"nom_type_presta"		=>	"varchar(50) default NULL",
		"commentaires"			=>	"mediumtext"
	);
	$spipmine_types_prestations_key = array(
		"PRIMARY KEY"			=>	"id_type_presta"
	);
	$tables_auxiliaires['spipmine_types_prestations'] = array(
		'field' => &$spipmine_types_prestations,
		'key' => &$spipmine_types_prestations_key
	);

	
	
	// structure de la table spipmine_types_facturation
	$spipmine_types_facturation = array(
		"id_type_facturation"	=>	"int(11) NOT NULL auto_increment",
		"nom_type_facturation"	=>	"varchar(50) default NULL",
		"commentaires"			=>	"mediumtext"
	);
	$spipmine_types_facturation_key = array(
		"PRIMARY KEY"			=>	"id_type_facturation"
	);
	$tables_auxiliaires['spipmine_types_facturation'] = array(
		'field' => &$spipmine_types_facturation,
		'key' => &$spipmine_types_facturation_key
	);


	// structure de la table spipmine_types_status
	$spipmine_types_status = array(
		"id_type_status"		=>	"int(11) NOT NULL auto_increment",
		"nom_type_status"		=>	"varchar(50) default NULL",
		"commentaires"			=>	"mediumtext"
	);
	$spipmine_types_status_key = array(
		"PRIMARY KEY"			=>	"id_type_status"
	);
	$tables_auxiliaires['spipmine_types_status'] = array(
		'field' => &$spipmine_types_status,
		'key' => &$spipmine_types_status_key
	);

	// structure de la table spipmine_types_reglements
	$spipmine_types_reglements = array(
		"id_type_reglement"		=>	"int(11) NOT NULL auto_increment",
		"nom_type_reglement"	=>	"varchar(50) default NULL",
		"commentaires"			=>	"mediumtext"
	);
	$spipmine_types_reglements_key = array(
		"PRIMARY KEY"			=>	"id_type_reglement"
	);
	$tables_auxiliaires['spipmine_types_reglements'] = array(
		'field' => &$spipmine_types_reglements,
		'key' => &$spipmine_types_reglements_key
	);

	
	return $tables_auxiliaires;
}



?>