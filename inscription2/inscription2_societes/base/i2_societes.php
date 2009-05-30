<?php
function i2_societes_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_auteurs_elargis'][] = 'societes';
	
	//-- Table des tables ----------------------------------------------------
	$interface['table_des_tables']['societes']='societes';

	return $interface;
}

function i2_societes_declarer_tables_principales($tables_principales){
	
	$spip_societes['id_societe'] = "BIGINT(21) NOT NULL";
	$spip_societes['nom'] = "VARCHAR(255) NOT NULL";
	$spip_societes['secteur'] = "VARCHAR(255) NOT NULL";
	$spip_societes['adresse'] = "TEXT NOT NULL";
	$spip_societes['code_postal'] = "VARCHAR(255) NOT NULL";
	$spip_societes['ville'] = "VARCHAR(255) NOT NULL";
	$spip_societes['id_pays'] = "SMALLINT NOT NULL";
	$spip_societes['telephone'] = "VARCHAR(255) NOT NULL";
	$spip_societes['fax'] = "VARCHAR(255) NOT NULL";
	$spip_societes['maj'] = "TIMESTAMP";
	
	$spip_societes_key = array('PRIMARY KEY' => 'id_societe', 'KEY id_pays' => 'id_pays');
	
	$tables_principales['spip_societes'] = array(
		'field' => &$spip_societes, 
		'key' => &$spip_societes_key);
	
	return $tables_principales;
}
?>