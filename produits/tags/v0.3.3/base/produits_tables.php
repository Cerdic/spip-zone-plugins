<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function produits_declarer_tables_interfaces($interface){
	// Déclaration des alias de table
	$interface['table_des_tables']['produits'] = 'produits';
	
	// Champs date sur les tables
	$interface['table_date']['produits'] = 'date';
	
	// Déclaration du titre
	$interface['table_titre']['produits'] = 'titre, "" as lang';
	
	return $interface;
}

function produits_declarer_tables_principales($tables_principales){
	//-- Table produits -----------------------------------------------------------
	$produits = array(
		'id_produit' => 'bigint(21) not null',
		'id_rubrique' => 'bigint(21) not null default 0',
		'id_secteur' => 'bigint(21) not null default 0',
		'titre' => 'text not null default ""',
		'reference' => 'varchar(255) not null default ""',
		'descriptif' => 'text not null default ""',
		'texte' => 'text not null default ""',
		'prix_ht' => 'float not null default 0',
		'taxe' => 'decimal(4,3) default null',
		'statut' => 'varchar(10)',
		'lang' => 'varchar(10) not null default ""',
		'date' => 'datetime not null default "0000-00-00 00:00:00"',
		'date_com' => 'datetime not null default "0000-00-00 00:00:00"',
		'maj' => 'timestamp'
	);
	
	$produits_cles = array(
		'PRIMARY KEY' => 'id_produit',
		'KEY id_rubrique' => 'id_rubrique',
		'KEY id_secteur' => 'id_secteur'
	);
	
	$tables_principales['spip_produits'] = array(
		'field' => &$produits,
		'key' => &$produits_cles,
		'join'=> array(
			'id_produit' => 'id_produit',
			'id_rubrique' => 'id_rubrique'
		)
	);

	return $tables_principales;
}

function produits_rechercher_liste_des_champs($tables){
	$tables['produit']['titre'] = 5;
	$tables['produit']['descriptif'] = 4;
	$tables['produit']['texte'] = 3;
	return $tables;
}

?>
