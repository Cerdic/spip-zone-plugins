<?php
/**
 * Plugin Quickvote pour Spip 2.1
 * Licence GPL
 * 
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

function quickvote_declarer_tables_principales($tables_principales){

	
	// Table QUICKVOTES : pour stocker les sondages  -----------------------------
	$spip_quickvotes = array(
		"id_quickvote" => "bigint(21) NOT NULL",  
    "titre"	=> "text NOT NULL DEFAULT ''",
    "reponse1"	=> "text NOT NULL DEFAULT ''",
    "reponse2"	=> "text NOT NULL DEFAULT ''",
    "reponse3"	=> "text NOT NULL DEFAULT ''",
    "reponse4"	=> "text NOT NULL DEFAULT ''",
    "reponse5"	=> "text NOT NULL DEFAULT ''",
    "reponse6"	=> "text NOT NULL DEFAULT ''",
    "reponse7"	=> "text NOT NULL DEFAULT ''",
    "reponse8"	=> "text NOT NULL DEFAULT ''",
    "reponse9"	=> "text NOT NULL DEFAULT ''",
    "reponse10"	=> "text NOT NULL DEFAULT ''",
    "hasard" => "tinyint(1) not null default 1",  // ordre reponse aleatoire ?
    "actif" => "tinyint(1) not null default 1",   // en cours ou cloturé ?
    "maj" => "TIMESTAMP" 
	);
	$spip_quickvotes_key = array(
		"PRIMARY KEY" => "id_quickvote"
	);

	$tables_principales['spip_quickvotes'] = array(
		'field' => &$spip_quickvotes,
		'key' => &$spip_quickvotes_key
	);
  
  // Table QUICKVOTES_VOTES : pour stocker les votes--------------  
	$spip_quickvotes_votes = array(
    "id_vote" => "bigint(21) NOT NULL",
    "id_quickvote" => "bigint(21) NOT NULL",
    "reponse" => "VARCHAR(255) NOT NULL",
    "ip"	=> "VARCHAR(255) NOT NULL",
    "maj" => "TIMESTAMP"
	); 
  
  $spip_quickvotes_votes_key = array(
		"PRIMARY KEY" => "id_vote"
	);     


	$tables_principales['spip_quickvotes_votes'] = array(
		'field' => &$spip_quickvotes_votes,
		'key' => &$spip_quickvotes_votes_key
	);  
	
	return $tables_principales;
}



function quickvote_declarer_tables_interfaces($interface){
	// definir les jointures possibles
	$interface['table_des_tables']['quickvotes'] = 'quickvotes';
  $interface['table_des_tables']['quickvotes_votes']  = 'quickvotes_votes';
  
  $interface['table_titre']['quickvotes'] = 'titre, "" as lang';
	
	// Traitement automatique des champs des quickvotes 
	//$interface['table_des_traitements']['TITRE'][]= _TRAITEMENT_TYPO; // ?

	return $interface;
}

?>