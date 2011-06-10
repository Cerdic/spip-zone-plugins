<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function gis_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_gis'][] = 'gis_liens';
	$interface['tables_jointures']['spip_gis_liens'][] = 'gis';
	$interface['tables_jointures']['spip_articles'][] = 'gis_liens';
	$interface['tables_jointures']['spip_auteurs'][] = 'gis_liens';
	$interface['tables_jointures']['spip_breves'][] = 'gis_liens';
	$interface['tables_jointures']['spip_documents'][] = 'gis_liens';
	$interface['tables_jointures']['spip_groupes_mots'][] = 'gis_liens';
	$interface['tables_jointures']['spip_mots'][] = 'gis_liens';
	$interface['tables_jointures']['spip_rubriques'][] = 'gis_liens';
	$interface['tables_jointures']['spip_syndic'][] = 'gis_liens';
	
	$interface['table_des_tables']['gis']='gis';
	$interface['table_des_tables']['gis_liens']='gis_liens';

	// Titre pour url
	$interface['table_titre']['gis'] = "titre, '' AS lang";
	
	return $interface;
}

function gis_declarer_tables_principales($tables_principales){
	$spip_gis = array(
		"id_gis" => "bigint(21) NOT NULL",
		"titre" 	=> "varchar(255) NOT NULL DEFAULT ''",
		"descriptif" => "text NOT NULL DEFAULT ''",
		"lat" => "float(21) NULL NULL",
		"lon" => "float(21) NULL NULL",
		"zoom" => "tinyint(4) NULL NULL",
		"adresse" => "text NOT NULL DEFAULT ''",
		"pays" => "text NOT NULL DEFAULT ''",
		"code_pays" => "varchar(255) NOT NULL DEFAULT ''",
		"region" => "text NOT NULL DEFAULT ''",
		"ville" => "text NOT NULL DEFAULT ''",
		"code_postal" => "varchar(255) NOT NULL DEFAULT ''"
		);
	
	$spip_gis_key = array(
		"PRIMARY KEY" => "id_gis");
	
	$tables_principales['spip_gis'] = array(
		'field' => &$spip_gis,
		'key' => &$spip_gis_key);
		
	return $tables_principales;
}

function gis_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_gis_liens = array(
		"id_gis" 	=> "bigint(21) NOT NULL",
		"objet" 	=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"id_objet" 	=> "bigint(21) NOT NULL");
	
	$spip_gis_liens_key = array(
		"PRIMARY KEY" 	=> "id_gis,id_objet,objet",
		"KEY id_objet" => "id_gis");
	
	$tables_auxiliaires['spip_gis_liens'] = array(
		'field' => &$spip_gis_liens,
		'key' => &$spip_gis_liens_key);
	
	return $tables_auxiliaires;
}

?>