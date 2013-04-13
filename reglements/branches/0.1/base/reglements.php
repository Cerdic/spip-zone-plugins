<?php
/**
 * Plugin Réglements - Facturer avec Spip 2.0
 * Licence GPL (c) 2010 - 2011
 * par Cyril Marion - Camille Lafitte
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

/***********************************************************************************/
/* DECLARATION DES TABLES INTERFACE
/***********************************************************************************/
function reglements_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['reglements'] = 'reglements';

	// -- Liaisons
	$interface['tables_jointures']['spip_reglements'][]= 'factures';

	// gerer le critere de date
	$interface['table_date']['reglements'] = 'date_reglement';

	return $interface;
}



/***********************************************************************************/
/* DECLARATION DES TABLES PRINCIPALES
/***********************************************************************************/
function reglements_declarer_tables_principales($tables_principales){

	// structure de la table reglements
	$reglements = array(
		"id_reglement"			=>	"int(11) NOT NULL auto_increment",
	        "id_objet"		   	=> 	"BIGINT(21) NOT NULL",
	        "objet"      			=> 	"VARCHAR(25) NOT NULL",
		"montant"			=>	"decimal(18,2) DEFAULT NULL",
		"date_reglement"		=>	"DATETIME NULL NULL",
		"type_reglement"		=>	"TEXT DEFAULT NULL"
	);
	$reglements_key = array(
		"PRIMARY KEY"			=>	"id_reglement"
	);
	$tables_principales['spip_reglements'] = array(
		'field' => &$reglements,
		'key' => &$reglements_key
	);
	
	return $tables_principales;
}
?>
