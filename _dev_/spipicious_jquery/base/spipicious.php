<?php
/*
 * spipicious
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * Quentin Drouet
 * Erational
 * 
 * 2007-2008 - Distribue sous licence GNU/GPL
 *
 */

function spipicious_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_mots'][] = 'spipicious';
	$interface['tables_jointures']['spip_mots'][]= 'mots_documents';
	$interface['tables_jointures']['spip_auteurs'][]= 'spipicious';
	//-- Table des tables ----------------------------------------------------
	$interface['table_des_tables']['spipicious']='spipicious';

	return $interface;
}

function spipicious_declarer_tables_principales($tables_principales){
	$spip_spipicious = array(
	  	"id_mot" 	    => "bigint(21) NOT NULL",
	  	"id_auteur" 	=> "bigint(21) NOT NULL",
	  	"id_article" 	=> "bigint(21) NOT NULL",
		"id_rubrique" 	=> "bigint(21) NOT NULL",
		"id_document" 	=> "bigint(21) NOT NULL",
	    "position"    => "int(10) NOT NULL",
		"maj" => "TIMESTAMP");
	
	$spip_spipicious_key = array(
		"KEY id_mot" 	=> "id_mot",
		"KEY id_auteur" => "id_auteur");
		
	 	$tables_principales['spip_spipicious'] = array(
	  	'field' => &$spip_spipicious,
	  	'key' => &$spip_spipicious_key);
		
	return $tables_principales;
}

function spipicious_declarer_tables_auxiliaires($tables_auxiliaires){
	
	//-- Table de relations MOTS_EVENEMENTS----------------------
	$spip_mots_documents = array(
			"id_mot"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
			"id_document"	=> "BIGINT (21) DEFAULT '0' NOT NULL");
	
	$spip_mots_documents_key = array(
			"PRIMARY KEY"	=> "id_mot, id_document",
			"KEY id_evenement"	=> "id_document");
	
	$tables_auxiliaires['spip_mots_documents'] = array(
		'field' => &$spip_mots_documents,
		'key' => &$spip_mots_documents_key);

	return $tables_auxiliaires;
}
?>
