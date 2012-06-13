<?php

/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational
 *
 * © 2007-2012 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function spipicious_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_mots'][] = 'spipicious';
	$interface['tables_jointures']['spip_mots'][]= 'mots_documents';
	$interface['tables_jointures']['spip_documents'][]= 'mots_documents';
	$interface['tables_jointures']['spip_auteurs'][]= 'spipicious';
	$interface['tables_jointures']['spip_articles'][] = 'spipicious';
	$interface['tables_jointures']['spip_breves'][] = 'spipicious';
	$interface['tables_jointures']['spip_documents'][] = 'spipicious';
	$interface['tables_jointures']['spip_rubriques'][] = 'spipicious';
	$interface['tables_jointures']['spip_syndic'][] = 'spipicious';

	//-- Table des tables ----------------------------------------------------
	$interface['table_des_tables']['spipicious']='spipicious';

	return $interface;
}

function spipicious_declarer_tables_principales($tables_principales){
	$spip_spipicious = array(
	  	"id_mot"	=> "bigint(21) NOT NULL",
	  	"id_auteur"	=> "bigint(21) NOT NULL",
		"id_objet"	=> "bigint(21) NOT NULL",
	  	"objet"		=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"position"	=> "int(10) NOT NULL",
		"statut"	=> "varchar(10) DEFAULT 'publie' NOT NULL",
		"maj"		=> "TIMESTAMP");

	$spip_spipicious_key = array(
		"PRIMARY KEY"	=> "id_mot, id_auteur, objet, id_objet",
		"KEY id_auteur" => "id_auteur");

	$tables_principales['spip_spipicious'] = array(
		'field' => &$spip_spipicious,
		'key' => &$spip_spipicious_key);

	return $tables_principales;
}
?>
