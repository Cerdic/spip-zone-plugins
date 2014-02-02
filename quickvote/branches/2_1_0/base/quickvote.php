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
		'id_quickvote' => "BIGINT NOT NULL",
		'titre'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse1'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse2'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse3'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse4'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse5'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse6'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse7'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse8'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse9'	=> "TEXT NOT NULL DEFAULT ''",
		'reponse10'	=> "TEXT NOT NULL DEFAULT ''",
		'hasard' => "BOOLEAN NOT NULL DEFAULT 1",  // TRUE||1="affichage aleatoire" FALSE||0="affichage dans l'ordre"
		'actif' => "BOOLEAN NOT NULL DEFAULT 1",   // TRUE||1="en cours" FALSE||0="cloturé"
		'maj' => "TIMESTAMP"
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
		'id_vote' => "BIGINT NOT NULL",
		'id_quickvote' => "BIGINT NOT NULL",
		'reponse' => "VARCHAR(9) NOT NULL", // reponseN avec 0<N<11 (il serait plus economique/efficace de stocker uniquement N...)
		'ip'	=> "VARCHAR(15) NOT NULL", // VARCHAR(15) ou INT UNSIGNED pour IPv4, VARCHAR(39) pour IPv6 (inclus les mappages IPv4 qui prefixent "::ffff:" pour un total de 22 caracteres)... cf http://stackoverflow.com/a/3455340
		'maj' => "TIMESTAMP"
	);
	$spip_quickvotes_votes_key = array(
		"PRIMARY KEY" => "id_vote" // le couple (id_quickvote,ip) est une bonne cle primaire qui fait l'economie de ce champ...
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
	$interface['table_des_tables']['quickvotes_votes'] = 'quickvotes_votes';

	$interface['table_titre']['quickvotes'] = 'titre, "" as lang';

	// Traitement automatique des champs des quickvotes	//$interface['table_des_traitements']['TITRE'][]= _TRAITEMENT_TYPO; // ?
	$interface['table_des_traitements']['REPONSE1'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE2'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE3'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE4'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE5'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE6'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE7'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE8'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE9'][] = 'propre(%s)';
	$interface['table_des_traitements']['REPONSE10'][] = 'propre(%s)';

	return $interface;
}

?>