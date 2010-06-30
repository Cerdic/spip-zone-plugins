<?php

/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/***********************************************************************************/
/* DECLARATION DES TABLES PRINCIPALES
/***********************************************************************************/
function sm_factures_declarer_tables_principales($tables_principales){

	// structure de la table spipmine_factures
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
		"nom_client"			=>	"varchar(50) default NULL",
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
		'field' => &$spipmine_factures,
		'key' => &$spipmine_factures_key
	);

	return $tables_principales;

}

?>
