<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function metas_declarer_tables_interfaces($interface){
	// definir les jointures possibles
	$interface['tables_jointures']['spip_metas'][] = 'metas_liens';
	$interface['tables_jointures']['spip_metas_liens'][] = 'metas';
	$interface['tables_jointures']['spip_articles'][] =  'metas_liens';
	$interface['tables_jointures']['spip_rubriques'][] = 'metas_liens';
	$interface['tables_jointures']['spip_breves'][] = 'metas_liens';
	$interface['tables_jointures']['spip_auteurs'][] = 'metas_liens';
	$interface['tables_jointures']['spip_documents'][] = 'metas_liens';

	// definir les noms raccourcis pour les <BOUCLE_(metas) ...
	$interface['table_des_tables']['metas']='metas';
	$interface['table_des_tables']['metas_liens']='metas_liens';

	// Titre pour url
	$interface['table_titre']['metas'] = "title, '' AS lang";
	return $interface;
}

function metas_declarer_tables_principales($tables_principales){
	// definition de la table metas
	$spip_metas = array(
		"id_meta" => "BIGINT(21) NOT NULL auto_increment",
	    "titre" => "VARCHAR(255) NOT NULL",
	    "description" => "VARCHAR(255) NOT NULL",
        "keywords" => "TEXT NOT NULL",
        "canonical" => "TEXT NOT NULL",
		"maj" 	=> "TIMESTAMP");

	// definir les cle primaire et secondaires
	$spip_metas_key = array(
		"PRIMARY KEY" => "id_meta");

	// inserer dans le tableau
	$tables_principales['spip_metas'] = array(
		'field' => &$spip_metas,
		'key' => &$spip_metas_key);

	return $tables_principales;
}

function metas_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_metas_liens = array(
		"id_meta" => "BIGINT(21) NOT NULL",
	    "id_objet" => "BIGINT(21) NOT NULL",
	    "objet" => "VARCHAR(255) NOT NULL"
	);

	$spip_metas_liens_key = array(
		"PRIMARY KEY" 	=> "id_meta, id_objet, objet");

	$tables_auxiliaires['spip_metas_liens'] = array(
		'field' => &$spip_metas_liens,
		'key' => &$spip_metas_liens_key);

	return $tables_auxiliaires;
}
?>