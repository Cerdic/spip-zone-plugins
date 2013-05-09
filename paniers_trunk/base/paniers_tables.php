<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function paniers_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['paniers'] = 'paniers';
	$interface['table_des_tables']['paniers_liens'] = 'paniers_liens';
	
	//-- Jointures ----------------------------------------------------
	$interface['tables_jointures']['spip_auteurs'][]= 'paniers';
		
	$interface['table_date']['paniers'] = 'date';
	
	return $interface;
}

function paniers_declarer_tables_principales($tables_principales){
	//-- Table paniers -----------------------------------------------------------
	$paniers = array(
		'id_panier' => 'bigint(21) not null',
		'id_auteur' => 'bigint(21) not null default 0',
		'cookie' => 'varchar(255) not null default ""',
		'statut' => 'varchar(20) not null default "encours"', // Un panier pourrait être "encours", "commande", "paye", "envoye", "retour", "retour_partiel"
		'date' => 'datetime not null default "0000-00-00 00:00:00"',
		'maj' => 'timestamp not null',
	);
	
	$paniers_cles = array(
		'PRIMARY KEY' => 'id_panier'
	);
	
	$tables_principales['spip_paniers'] = array(
		'field' => &$paniers,
		'key' => &$paniers_cles,
		'join'=> array(
			'id_panier' => 'id_panier'
		)
	);

	return $tables_principales;
}

function paniers_declarer_tables_auxiliaires($tables_auxiliaires){
	
	//-- Table de relations des paniers avec les objets
	$spip_paniers_liens = array(
		'id_panier' => 'bigint(21) not null default 0',
		'id_objet' => 'bigint(21) not null default 0',
		'objet' => 'varchar(25) not null default ""',
		'quantite' => 'int not null default 1',
	);
	
	$spip_paniers_liens_cles = array(
		'PRIMARY KEY' => 'id_panier, id_objet, objet',
		'KEY id_panier' => 'id_panier'
	);
	
	$tables_auxiliaires['spip_paniers_liens'] = array(
		'field' => &$spip_paniers_liens,
		'key' => &$spip_paniers_liens_cles
	);
	
	return $tables_auxiliaires;
}

?>
