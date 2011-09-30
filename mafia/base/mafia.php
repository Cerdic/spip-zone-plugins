<?php
/**
 * Plugin mafia pour Spip 2.0
 * Licence GPL (c) 2011 Anne-lise Martenot
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

function mafia_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['mafias'] = 'mafias';
	// -- Liaisons
	$interface['tables_jointures']['spip_auteurs'][] = 'mafias';
	$interface['tables_jointures']['spip_mafias'][] = 'auteurs';

	return $interface;
}

function mafia_declarer_tables_principales($tables_auxiliaires){
	$mafias = array(
		"id_parrain" 	=> "bigint(21) NOT NULL",
		"id_filleul" 	=> "bigint(21) NOT NULL"
	);
	
	$mafias_key = array(
		"PRIMARY KEY" 	=> "id_parrain, id_filleul",
	);
		
	$tables_auxiliaires['spip_mafias'] = array(
		'field' => &$mafias,
		'key' => &$mafias_key,
	);
	
	return $tables_auxiliaires;
}

?>
