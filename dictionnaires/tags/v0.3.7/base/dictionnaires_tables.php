<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function dictionnaires_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['dictionnaires'] = 'dictionnaires';
	$interface['table_des_tables']['definitions'] = 'definitions';
	
	$interface['table_date']['definitions'] = 'date';
	
	$interface['table_titre']['dictionnaires'] = 'titre, "" as lang';
	$interface['table_titre']['definitions'] = 'titre, lang';
	
	// Traitement automatique des champs des dictionnaires
	$interface['table_des_traitements']['TITRE'][]= _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['DESCRIPTION'][]= _TRAITEMENT_RACCOURCIS;
	
	return $interface;
}

function dictionnaires_declarer_tables_principales($tables_principales){
	//-- Table dictionnaires -----------------------------------------------------------
	$dictionnaires = array(
		'id_dictionnaire' => 'bigint(21) not null',
		'titre' => 'text not null default ""',
		'descriptif' => 'text not null default ""',
		'type_defaut' => 'varchar(255) not null default ""',
		'actif' => 'tinyint(1) not null default 0',
		'maj' => 'timestamp',
	);
	
	$dictionnaires_cles = array(
		'PRIMARY KEY' => 'id_dictionnaire'
	);
	
	$tables_principales['spip_dictionnaires'] = array(
		'field' => &$dictionnaires,
		'key' => &$dictionnaires_cles,
		'join'=> array(
			'id_dictionnaire' => 'id_dictionnaire'
		)
	);
	
	//-- Table definitions -----------------------------------------------------------
	$definitions = array(
		'id_definition' => 'bigint(21) not null',
		'id_dictionnaire' => 'bigint(21) not null',
		'titre' => 'text not null default ""',
		'texte' => 'text not null default ""',
		'termes' => 'text not null default ""',
		'type' => 'varchar(255) not null default ""',
		'casse' => 'tinyint(1) not null default 0',
		'statut' => 'varchar(255) not null default "prop"',
		'lang' => 'varchar(10) not null default ""',
		'date' => 'datetime default "0000-00-00 00:00:00" not null',
		'maj' => 'timestamp',
	);
	
	$definitions_cles = array(
		'PRIMARY KEY' => 'id_definition',
		'KEY id_dictionnaire' => 'id_dictionnaire'
	);
	
	$tables_principales['spip_definitions'] = array(
		'field' => &$definitions,
		'key' => &$definitions_cles,
		'join'=> array(
			'id_definition' => 'id_definition'
		)
	);

	return $tables_principales;
}

function dictionnaires_declarer_tables_auxiliaires($tables_auxiliaires){
	//-- Table de relations definitions_liens -----------------------------------------
	$spip_definitions_liens = array(
		'id_definition' => 'bigint(21) not null default 0',
		'objet' => 'varchar(255) not null default ""',
		'id_objet' => 'bigint(21) not null default 0',
	);
	
	$spip_definitions_liens_cles = array(
		'PRIMARY KEY' => 'id_definition, objet, id_objet',
		'KEY id_definition' => 'id_definition'
	);
	
	$tables_auxiliaires['spip_definitions_liens'] = array(
		'field' => &$spip_definitions_liens,
		'key' => &$spip_definitions_liens_cles
	);
	
	return $tables_auxiliaires;
}

?>
