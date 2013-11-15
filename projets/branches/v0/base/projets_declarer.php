<?php
/**
 * Plugin SPIP-Projet
 * Licence GPL
 * Eric Lupinacci, Quentin Drouet
 *
 * Declaration des tables du plugin
 *
 */
function projets_declarer_tables_principales($tables_principales){
	$spip_projets = array(
		"id_projet" 	=> "bigint(21) NOT NULL",
		"id_parent"		=> "bigint(21) DEFAULT '0' NOT NULL",
		"titre"			=> "text NOT NULL",
		"descriptif"	=> "text DEFAULT '' NOT NULL",
		"texte"			=> "longtext DEFAULT '' NOT NULL",
		"date"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_modif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"statut"		=> "varchar(10) DEFAULT '0' NOT NULL",
		"maj"			=> "TIMESTAMP"
	);

	$spip_projets_key = array(
		"PRIMARY KEY"	=> "id_projet"
	);

	$tables_principales['spip_projets'] = array(
		'field' => &$spip_projets,
		'key' => &$spip_projets_key);

	return $tables_principales;
}

function projets_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_projets_liens = array(
		"id_projet" => "bigint(21) NOT NULL",
		"id_objet" 	=> "bigint(21) NOT NULL",
		"objet" 	=> "VARCHAR(25) DEFAULT '' NOT NULL",
		"type" 		=> "VARCHAR(25) DEFAULT '' NOT NULL");

	$spip_projets_liens_key = array(
		"PRIMARY KEY" 	=> "id_projet,id_objet,objet",
		"KEY id_objet" => "id_projet");

	$tables_auxiliaires['spip_projets_liens'] = array(
		'field' => &$spip_projets_liens,
		'key' => &$spip_projets_liens_key);

	return $tables_auxiliaires;
}

function projets_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_projets'][] = 'projets_liens';
	$interface['tables_jointures']['spip_auteurs'][] = 'projets_liens';
	$interface['tables_jointures']['spip_articles'][] = 'projets_liens';
	$interface['tables_jointures']['spip_rubriques'][] = 'projets_liens';
	$interface['tables_jointures']['spip_documents'][] = 'projets_liens';
	$interface['tables_jointures']['spip_mots'][] = 'projets_liens';
	$interface['table_des_tables']['projets']='projets';
	$interface['table_des_tables']['projets_liens']='projets_liens';

	return $interface;
}
?>