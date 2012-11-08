<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr) V0.1
* @author: Pierre KUHN V1
*
* Copyright (c) 2011-12
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function anaclic_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_documents'][] = 'clics';
	$interface['table_des_tables']['clics']	= 'clics';

	return $interface;
}

function anaclic_declarer_tables_auxiliaires($tables_auxiliaires){

	$spip_clics = array(
			"id_clic"	=> 	"bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"	=> 	"bigint(21) DEFAULT '0' NOT NULL",
			"objet"		=>	"VARCHAR (25) DEFAULT '' NOT NULL",
			"ip"		=>	"VARCHAR (30) DEFAULT '' NOT NULL",
			"maj"		=>	"TIMESTAMP"
	);

	$spip_clics_key = array(
			"PRIMARY KEY"	=> "id_clic,id_objet,objet",
			"KEY id_clic"	=> "id_clic"
	);

	$tables_auxiliaires['spip_clics'] = array(
		'field' => &$spip_clics,
		'key' => &$spip_clics_key
	);

	return $tables_auxiliaires;
}

?>
