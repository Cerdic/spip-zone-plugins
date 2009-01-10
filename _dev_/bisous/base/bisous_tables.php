<?php
/*
 * Plugin SPIP Bisous pour Spip 2.0
 * Licence GPL
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function bisous_declarer_tables_interfaces($interface){
	//-- Alias
	$interface['table_des_tables']['bisous'] = 'bisous';	

	return $interface;
}

function bisous_declarer_tables_principales($tables_principales){

	$spip_bisous = array(
		'id_bisou' => 'bigint(21) NOT NULL',
		'id_donneur' => 'bigint(21) DEFAULT "0" NOT NULL',
		'id_receveur' => 'bigint(21) DEFAULT "0" NOT NULL',
		'date' => 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL'
	);
	
	$spip_bisous_cles = array(
		'PRIMARY KEY' => 'id_bisou'
	);
	
	$tables_principales['spip_bisous'] = array(
		'field' => &$spip_bisous,
		'key' => &$spip_bisous_cles
	);
	
/*	
	$spip_bisous = array(
		'id_bisou' => 'bigint(21) NOT NULL',
		'id_donneur' => 'bigint(21) DEFAULT "0" NOT NULL',
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
		'id_objet' => 'bigint(21) DEFAULT "0" NOT NULL',
		'date' => 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL'
	);
	
	$spip_bisous_cles = array(
		'PRIMARY KEY' => 'id_bisou',
		'KEY id_donneur' => 'id_donneur',
	);
	
	$tables_principales['spip_bisous'] = array(
		'field' => &$spip_bisous,
		'key' => &$spip_bisous_cles,
		'join' => array(
			'id_donneur'=>array('auteurs','id_auteur')
		)
	);	
*/
	return $tables_principales;

}

?>
