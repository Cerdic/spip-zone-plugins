<?php
/**
 * Plugin mots-auteurs pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 *
 */

function mots_auteurs_declarer_tables_interfaces($tables_interface){

	// -- Prise en compte de la nouvelle table
	$interface['table_des_tables']['mots_auteurs'] = 'mots_auteurs';

	// -- Liaisons mots/auteurs
	$interface['tables_jointures']['spip_mots_auteurs'][]= 'auteurs';
	$interface['tables_jointures']['spip_auteurs'][]= 'mots_auteurs';

	return $tables_interface;
}

function mots_auteurs_declarer_tables_principales($tables_principales){

	$spip_mots_auteurs = array(
		"id_mot"		=> "bigint(21) NOT NULL",
		"id_auteur"		=> "bigint(21) NOT NULL",
	);
	$spip_mots_auteurs_key = array(
		"PRIMARY KEY"	=> "id_mot, id_auteur",
		"KEY id_mot"	=> "id_mot"
	);
	$tables_principales['spip_mots_auteurs'] = array(
		'field'=>&$spip_mots_auteurs,
		'key'=>$spip_mots_auteurs_key
	);
	
	return $tables_principales;
}

?>