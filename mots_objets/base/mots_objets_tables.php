<?php
/**
 * Plugin mots-objets pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 *
 */

function mots_objets_declarer_tables_interfaces($tables_interface){

	// -- Prise en compte de la nouvelle table
	$interface['table_des_tables']['mots_auteurs'] = 'mots_auteurs';

	// -- Liaisons mots/auteurs
	$interface['tables_jointures']['spip_mots_auteurs'][]= 'auteurs';
	$interface['tables_jointures']['spip_auteurs'][]= 'mots_auteurs';

	// documents...
	$interface['table_des_tables']['mots_documents'] = 'mots_documents';

	// -- Liaisons mots/auteurs
	$interface['tables_jointures']['spip_mots_documents'][]= 'documents';
	$interface['tables_jointures']['spip_documents'][]= 'mots_documents';

	
	return $tables_interface;
}

function mots_objets_declarer_tables_auxiliaires($tables_auxiliaires){

	$spip_mots_auteurs = array(
		"id_mot"		=> "bigint(21) NOT NULL",
		"id_auteur"		=> "bigint(21) NOT NULL",
	);
	$spip_mots_auteurs_key = array(
		"PRIMARY KEY"	=> "id_mot, id_auteur",
		"KEY id_mot"	=> "id_mot"
	);
	$tables_auxiliaires['spip_mots_auteurs'] = array(
		'field'=>&$spip_mots_auteurs,
		'key'=>$spip_mots_auteurs_key
	);

	
	$spip_mots_documents = array(
		"id_mot"		=> "bigint(21) NOT NULL",
		"id_document"	=> "bigint(21) NOT NULL",
	);
	$spip_mots_documents_key = array(
		"PRIMARY KEY"	=> "id_mot, id_document",
		"KEY id_mot"	=> "id_mot"
	);
	$tables_auxiliaires['spip_mots_documents'] = array(
		'field'=>&$spip_mots_documents,
		'key'=>$spip_mots_documents_key
	);
	
	return $tables_auxiliaires;
}

?>
