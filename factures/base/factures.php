<?php
/**
 * Plugin Devis et Factures - Facturer avec Spip 2.0
 * Licence GPL (c) 2010 - 2011
 * par Cyril Marion - Camille Lafitte
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

/***********************************************************************************/
/* DECLARATION DES TABLES INTERFACE
/***********************************************************************************/
function factures_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['factures'] = 'factures';
	$interface['table_des_tables']['lignes_factures'] = 'lignes_factures';

	// -- Liaisons
	$interface['tables_jointures']['spip_factures'][]= 'auteurs';
	$interface['tables_jointures']['spip_auteurs'][]= 'factures';

	// gerer le critere de date
	$interface['table_date']['factures'] = 'date_facture';
	$interface['table_date']['factures'] = 'date_payement';
	$interface['table_date']['factures'] = 'fin_validite';

	// -- traitemnets sur les champs texte
	$interface['table_des_traitements']['DESIGNATION'][] = _TRAITEMENT_RACCOURCIS;

	return $interface;
}



/***********************************************************************************/
/* DECLARATION DES TABLES PRINCIPALES
/***********************************************************************************/
function factures_declarer_tables_principales($tables_principales){

	// structure de la table factures
	$factures = array(
		"id_facture"			=>	"int(11) NOT NULL auto_increment",
		"id_organisation"		=>	"int(11) default NULL",
		"num_facture"			=>	"varchar(50) default NULL", // un numéro unique selon l'organisation
		"num_devis"				=>	"varchar(50) default NULL", // l'éventuel numéro de devis d'où est issu la facture
		"id_type_facture"		=>	"int(11) default NULL",
		"delais_validite"		=>	"int(11) default NULL",
		"date_facture"			=>	"DATETIME NULL default NULL",
		"date_payement"			=>	"DATETIME NULL NULL",
		"reglement"				=>	"varchar(50) default NULL",
		"fin_validite"			=>	"DATETIME NULL default NULL",
		"id_auteur"				=>	"int(11) default NULL", // precedemment id_organisation
		"id_projet"				=>	"int(11) default NULL",
		"id_type_presta"		=>	"int(11) default NULL",
		"montant"				=>	"decimal(18,2) default NULL",
		"delais"				=>	"varchar(50) default NULL", // delais de livraison prévus
		"charge_estimee"		=>	"float default NULL", // charge de travail estimee en heures
		"nb_heures_vendues"		=>	"decimal(18,2) default NULL", // total des heures de la facture
		"libelle_facture"		=>	"mediumtext",
		"nota_bene"				=>	"mediumtext"
	);
	$factures_key = array(
		"PRIMARY KEY"			=>	"id_facture",
		"KEY date_facture"		=>	"date_facture",
		"KEY num_facture"		=>	"num_facture"
	);
	$tables_principales['spip_factures'] = array(
		'field' => &$factures,
		'key' => &$factures_key
	);
	
	// structure de la table factures
	$lignes_factures = array(
		"id_ligne"				=>	"int(11) NOT NULL auto_increment",
		"id_facture"			=>	"int(11) default NULL",
		"position"				=>	"int(11) default NULL",
		"quantite"				=>	"float default NULL",
		"unite"					=>	"varchar(50) default NULL",
		"designation"			=>	"text",
		"prix_unitaires_ht"		=>	"decimal(18,2) default NULL",
		"commentaire"			=>	"mediumtext"
	);
	$lignes_factures_key = array(
		"PRIMARY KEY"			=>	"id_ligne",
		"KEY id_facture"		=>	"id_facture"
	);
	$tables_principales['spip_lignes_factures'] = array(
		'field' => &$lignes_factures,
		'key' => &$lignes_factures_key
	);

	// structure de la table factures
	$types_facture = array(
		"id_type_facture"				=>	"int(11) NOT NULL auto_increment",
		"titre"                         =>  "text",
		"descriptif"                    =>  "text"
	);
	$types_facture_key = array(
		"PRIMARY KEY"			=>	"id_type_facture"
	);
	$tables_principales['spip_types_facture'] = array(
		'field' => &$types_facture,
		'key' => &$types_facture_key
	);
	
	return $tables_principales;
}
?>
