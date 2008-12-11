<?php
/*
 * Plugin SPIP Bisous pour Spip 2.0
 * Licence GPL
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function bisous_declarer_tables_interfaces($interface){
	
	//-- Jointures ----------------------------------------------------
	$interface['tables_jointures']['spip_auteurs'][]= 'auteurs';
	$interface['tables_jointures']['spip_auteurs'][]= 'bisous';
	
	$interface['exceptions_des_tables']['auteurs']['id_donneur'] = 'id_auteur';
	$interface['exceptions_des_tables']['auteurs']['id_receveur'] = 'id_auteur';

	return $interface;

}

function bisous_declarer_tables_auxiliaires($tables_auxiliaires){

	$spip_bisous = array(
		'id_donneur' => 'bigint(21) DEFAULT '0' NOT NULL',
		'id_receveur' => 'bigint(21) DEFAULT '0' NOT NULL',
		'date' => 'datetime DEFAULT '0000-00-00 00:00:00' NOT NULL'
	);
	
	$spip_bisous_cles = array(
		'PRIMARY KEY' => 'id_donneur, id_receveur'
	);
	
	return $tables_auxiliaires;

}

?>
