<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function listeimc_declarer_tables_principales($tables_principales)
{
	$imc = array(
		"id_imc" => "BIGINT(21) NOT NULL",
		"id_groupe" => "BIGINT(21) NOT NULL",
		"url" => "VARCHAR(32) NOT NULL",
		"libelle" => "VARCHAR(32) NOT NULL"
	);

	$cles_imc = array(
		"PRIMARY KEY" => "id_imc"
	);

	$tables_principales['spip_listeimc_imc'] = array(
		'field' => &$imc,
		'key' => &$cles_imc
	);



	$groupe = array(
		"id_groupe" => "BIGINT(21) NOT NULL",
		"libelle" => "VARCHAR(32) NOT NULL"
	);

	$cles_groupe = array(
		"PRIMARY KEY" => "id_groupe"
	);

	$tables_principales['spip_listeimc_groupe'] = array(
		'field' => &$groupe,
		'key' => &$cles_groupe
	);

	return $tables_principales;
}


function listeimc_declarer_tables_auxiliaires($tables_auxiliaires)
{
/*	$imc_groupe = array(
		"id_groupe" => "BIGINT(21) NOT NULL",
		"id_imc" => "BIGINT(21) NOT NULL"
	);

	$imc_groupe_cles = array(
		"PRIMARY KEY" => "id_imc, id_groupe"
	);

	$tables_auxiliaires['spip_listeimc_imc_groupe'] = array(
		'field' => &$imc_groupe,
		'key' => &$imc_groupe_cles
	);
*/
	return $tables_auxiliaires;
}

function listeimc_declarer_tables_interfaces($tables_interfaces)
{
	
//	$tables_interfaces['tables_jointures']['spip_listeimc_imc'][] = 'spip_listeimc_imc_groupe';
//	$tables_interfaces['tables_jointures']['spip_listeimc_groupe'][] = 'spip_listeimc_imc_groupe';

	$tables_interfaces['table_des_tables']['listeimc_imc'] = 'listeimc_imc';
	$tables_interfaces['table_des_tables']['listeimc_groupe'] = 'listeimc_groupe';

	return $tables_interfaces;
}

?>