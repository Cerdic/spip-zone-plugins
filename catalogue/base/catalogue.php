<?php

// xxx_declarer_tables_interfaces est l'endroit ou l'on indique les raccourcis à utiliser comme table de boucle SPIP
function catalogue_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['catalogue_familles'] = 'familles';
	$interface['table_des_tables']['catalogue_produits'] = 'produits';
	$interface['table_des_tables']['catalogue_variantes'] = 'variantes';
	$interface['table_des_traitements']['titre']['catalogue_familles'] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['titre']['catalogue_produits'] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['titre']['catalogue_variantes'] = _TRAITEMENT_TYPO;	
	return $interface;
}

function catalogue_declarer_tables_principales($tables_principales){

	//-- Table cat_familles ------------------------------------------
	$catalogue_familles = array(
		"id_famille"	=> "bigint(21) NOT NULL",
		"id_parent" 	=> "bigint(21) NOT NULL",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "tinytext DEFAULT '' NOT NULL",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00",
		"maj"			=> "TIMESTAMP"
		);
	
	$catalogue_familles_key = array(
		"PRIMARY KEY"	=> "id_famille",
		);

	//-- Table cat_produits ------------------------------------------
	$catalogue_produits = array(
		"id_produit" 	=> "bigint(21) NOT NULL",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "tinytext DEFAULT '' NOT NULL",
		"prix_ht" 		=> "decimal(6,2) default NULL",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00",
		"maj"			=> "TIMESTAMP"
		);
	
	$catalogue_produits_key = array(
		"PRIMARY KEY"	=> "id_produit",
		);

	//-- Table cat_variantes ------------------------------------------
	$catalogue_variantes = array(
		"id_variante" 	=> "bigint(21) NOT NULL",
		"id_produit" 	=> "bigint(21) NOT NULL",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "tinytext DEFAULT '' NOT NULL",
		"prix_ht" 		=> "decimal(6,2) default NULL",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00",
		"maj"			=> "TIMESTAMP"
		);
	
	$catalogue_variantes_key = array(
		"PRIMARY KEY"	=> "id_variante",
		);
	
	$tables_principales['spip_familles'] =
		array('field' => &$catalogue_familles, 'key' => &$catalogue_familles_key);
		
	$tables_principales['spip_produits'] =
		array('field' => &$catalogue_produits, 'key' => &$catalogue_produits_key);

	$tables_principales['spip_variantes'] =
		array('field' => &$catalogue_variantes, 'key' => &$catalogue_variantes_key);
				
	return $tables_principales;
}

?>