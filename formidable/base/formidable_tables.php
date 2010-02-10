<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formidable_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['formidables'] = 'formidables';
	
	$interface['table_titre']['formidables'] = 'titre, lang';
	
	$interface['tables_jointures']['spip_formidables'][] = 'formidables_liens';
	$interface['tables_jointures']['spip_articles'][] = 'formidables_liens';
	$interface['tables_jointures']['spip_rubriques'][] = 'formidables_liens';
	
	return $interface;
}

function formidable_declarer_tables_principales($tables_principales){
	//-- Table formidables -----------------------------------------------------
	$formidables = array(
		"id_formidable" => "bigint(21) NOT NULL",
		"identifiant" => "varchar(200)",
		"titre" => "text NOT NULL",
		"descriptif" => "text",
		"message_ok" => "varchar(255) NOT NULL",
		"contenu" => "text NOT NULL",
		"traitement" => "text NOT NULL",
		"public" => "enum('non', 'oui') DEFAULT 'non' NOT NULL",
		"statut" => "varchar(10) NOT NULL",
		"modifiable" => "ENUM('non', 'oui') DEFAULT 'non'",
		"multiple" => "ENUM('non', 'oui') DEFAULT 'non'",
		"moderation" => "VARCHAR(10) DEFAULT 'posteriori'",
	);
	$formidables_cles = array(
		"PRIMARY KEY" => "id_formidable"
	);
	$tables_principales['spip_formidables'] = array(
		'field' => &$formidables,
		'key' => &$formidables_cles,
		'join'=> array(
			'id_formidable' => 'id_formidable'
		)
	);
	
	//-- Table formidables_reponses --------------------------------------------
	$formidables_reponses = array(
		"id_formidables_reponse" => "bigint(21) NOT NULL",
		"id_formidable" => "bigint(21) NOT NULL",
		"date" => "datetime NOT NULL",
		"ip" => "varchar(255) NOT NULL",
		"id_auteur" => "bigint(21) NOT NULL",
		"statut" => "varchar(10) NOT NULL",
		"maj" => "timestamp"
	);
	$formidables_reponses_cles = array(
		"PRIMARY KEY" => "id_formidables_reponse",
		"KEY id_formidable" => "id_formidable",
		"KEY id_auteur" => "id_auteur"
	);
	$tables_principales['spip_formidables_reponses'] = array(
		'field' => &$formidables_reponses,
		'key' => &$formidables_reponses_cles,
		'join'=> array(
			'id_formidables_reponse' => 'id_formidables_reponse',
			'id_formidable' => 'id_formidable',
			'id_auteur' => 'id_auteur'
		)
	);
	
	//-- Table formidables_reponses_champs -------------------------------------
	$formidables_reponses_champs = array(
		"id_formidables_reponse" => "bigint(21) NOT NULL",
		"nom" => "varchar(255) NOT NULL",
		"valeur" => "text NOT NULL DEFAULT ''",
		"maj" => "timestamp"
	);
	$formidables_reponses_champs_cles = array(
		"PRIMARY KEY" => "id_formidables_reponse, nom",
		"KEY id_formidables_reponse" => "id_formidables_reponse"
	);
	$tables_principales['spip_formidables_reponses_champs'] = array(
		'field' => &$formidables_reponses_champs,
		'key' => &$formidables_reponses_champs_cles
	);
	
	return $tables_principales;
}

function formidable_declarer_tables_auxiliaires($tables_auxiliaires){
	$formidables_liens = array(
		"id_formidable"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL"
	);

	$formidables_liens_cles = array(
		"PRIMARY KEY" => "id_formidable,id_objet,objet",
		"KEY id_formidable" => "id_formidable"
	);
	
	$tables_auxiliaires['spip_formidables_liens'] = array(
		'field' => &$formidables_liens,
		'key' => &$formidables_liens_cles
	);
	
	return $tables_auxiliaires;
}

function formidable_rechercher_liste_des_champs($tables){
	$tables['formidable']['titre'] = 5;
	$tables['formidable']['descriptif'] = 3;
	return $tables;
}

?>
