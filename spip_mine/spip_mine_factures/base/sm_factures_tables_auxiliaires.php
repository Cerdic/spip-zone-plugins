<?php 

/**
 * Plugin sm_factures pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

function sm_factures_declarer_tables_auxiliaires($tables_auxiliaires){

	// structure de la table spipmine_lignes_factures
	// liée à la table spipmine_factures
	$spipmine_lignes_factures = array(
		"id_ligne"				=>	"int(11) NOT NULL auto_increment",
		"id_facture"			=>	"int(11) default NULL",
		"position"				=>	"int(11) default NULL",
		"quantite"				=>	"float default NULL",
		"unite"					=>	"varchar(50) default NULL",
		"designation"			=>	"text default NULL",
		"prix_unitaire_ht"		=>	"int(11) default NULL",
		"commentaire"			=>	"mediumtext"
	);
	$spipmine_lignes_factures_key = array(
		"PRIMARY KEY"			=>	"id_ligne",
		"KEY id_facture"		=>	"id_facture"
	);

	$tables_auxiliaires['spipmine_lignes_factures'] = array(
		'field' => &$spipmine_lignes_factures,
		'key' => &$spipmine_lignes_factures_key
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

    return $tables_auxiliaires;

}

?>
