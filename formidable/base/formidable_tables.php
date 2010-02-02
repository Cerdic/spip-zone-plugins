<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formidable_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['formidables'] = 'formidables';
	
	$interface['table_titre']['formidables'] = 'titre, lang';
	
	// Traitement automatique des champs des formidables
	$interface['table_des_traitements']['TITRE'][]= _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['DESCRIPTION'][]= _TRAITEMENT_RACCOURCIS;
	
	return $interface;
}

function formidable_declarer_tables_principales($tables_principales){
	//-- Table formidables -----------------------------------------------------------
	$formidables = array(
		"id_formidable" => "bigint(21) NOT NULL",
		"identifiant" => "varchar(200)",
		"titre" => "text NOT NULL",
		"description" => "text NOT NULL",
		"contenu" => "text NOT NULL",
		"traitement" => "text NOT NULL"
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

	return $tables_principales;
}

#function formidable_declarer_tables_auxiliaires($tables_auxiliaires){
#	return $tables_auxiliaires;
#}

function formidable_rechercher_liste_des_champs($tables){
	$tables['formidable']['titre'] = 5;
	$tables['formidable']['description'] = 3;
	return $tables;
}

?p>
