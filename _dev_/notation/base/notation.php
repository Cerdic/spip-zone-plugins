<?php
/**
* Plugin Notation v.0.5
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Definition des tables
*  
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function notation_declarer_tables_principales($tables_principales){
	
	// cette table recoit les votes des participants...
	// c'est plus finalement une table de liaison entre
	// un votant et un objet
	$spip_notations = array(
		"id_notation" => "BIGINT(21) NOT NULL auto_increment",
		"objet"	=> "varchar(21) DEFAULT '' NOT NULL",
		"id_objet" => "BIGINT(21) NOT NULL DEFAULT '0'",
		//"id_article" => "BIGINT(21) NOT NULL DEFAULT '0'",
		//"id_forum" => "BIGINT(21) NOT NULL DEFAULT '0'",
		"id_auteur" => "BIGINT(21) NOT NULL",
		"ip"	=> "VARCHAR(255) NOT NULL",
		"note" => "TINYINT(1) NOT NULL",
		"maj" => "TIMESTAMP"
	);
	$spip_notations_key = array(
		"PRIMARY KEY" => "id_notation",
		"KEY objet" => "objet",
		"KEY id_objet" => "id_objet",
		//"KEY id_article" => "id_article",
		//"KEY id_forum" => "id_forum",
		"KEY id_auteur"	=> "id_auteur",
		"KEY ip" => "ip",
		"KEY note" => "note"
	);

	$tables_principales['spip_notations'] = array(
		'field' => &$spip_notations,
		'key' => &$spip_notations_key
	);
	
	return $tables_principales;
}


function notation_declarer_tables_auxiliaires($tables_auxiliaires){	
	// cette table est une sorte de vue sur la table notation
	// pour determiner la note moyenne, ponderee et le nombre de votes
	// d'un id d'objet particulier... Bref... 
	// elle ne sert a rien (ou a soulager le serveur sql) ?
	$spip_notations_objets = array(
		"objet"	=> "varchar(21) DEFAULT '' NOT NULL",
		"id_objet" => "BIGINT(21) NOT NULL DEFAULT '0'",
		"note" => "DOUBLE NOT NULL",
		"note_ponderee" => "DOUBLE NOT NULL",
		"nombre_votes" => "BIGINT(21) NOT NULL"
	);
	$spip_notations_objets_key = array(
		"KEY objet" => "objet",
		"KEY id_objet" => "id_objet",
		"KEY note" => "note",
		"KEY note_ponderee"	=> "note_ponderee",
		"KEY nombre_votes"	=> "nombre_votes"
	);

	$tables_auxiliaires['spip_notations_objets'] = array(
		'field' => &$spip_notations_objets,
		'key' => &$spip_notations_objets_key
	);
		
	return $tables_auxiliaires;
}


function notation_declarer_tables_interfaces($interface){
	// definir les jointures possibles
	$interface['table_des_tables']['notations'] = 'notations';
	$interface['table_des_tables']['notations_objets']  = 'notations_objets';
	
	$interface['tables_jointures']['spip_notations'][] = 'notations_objets';
	$interface['tables_jointures']['spip_notations_objets'][] = 'notations';
	$interface['tables_jointures']['spip_articles'][] = 'notations_objets';
	$interface['tables_jointures']['spip_notations_objets'][] = 'spip_articles';
	$interface['tables_jointures']['spip_forums'][] = 'notations_objets';
	return $interface;
}

?>
