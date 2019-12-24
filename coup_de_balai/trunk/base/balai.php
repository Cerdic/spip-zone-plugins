<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function balai_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['balai'] = 'balai';
	$interfaces['tables_jointures']['spip_articles'][] = 'balai';
	$interfaces['tables_jointures']['spip_balai'][] = 'articles';
	$interfaces['tables_jointures']['spip_rubriques'][] = 'balai';
	$interfaces['tables_jointures']['spip_balai'][] = 'rubriques';
	$interfaces['tables_jointures']['spip_balai'][] = 'auteurs';
	$interfaces['tables_jointures']['spip_auteurs'][] = 'balai';


	return $interfaces;
}


function balai_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_balai = array(
		"objet"		 => "varchar(21) DEFAULT '' NOT NULL",
		"id_objet" 	 => "BIGINT(21) NOT NULL DEFAULT '0'",
		"id_auteur" =>  "BIGINT(21) NOT NULL DEFAULT '0'",
		"date" =>  "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
	);

	$spip_balai_key = array(
		"PRIMARY KEY" => "objet, id_objet",
		"KEY objet" => "objet",
		"KEY id_objet" => "id_objet"
	);

	$tables_auxiliaires['spip_balai'] = array(
		'field' => &$spip_balai,
		'key' => &$spip_balai_key,
	);

	return $tables_auxiliaires;
}
