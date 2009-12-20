<?php

function cat_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['cat'] = 'cat';	
	$interface['table_des_traitements']['titre']['cat_produits'] = _TRAITEMENT_TYPO;
	return $interface;
}

function cat_declarer_tables_principales($tables_principales){

	//-- Table cat_familles ------------------------------------------
	$cat_familles = array(
			"id_famille" => "bigint(21) NOT NULL",
			"id_parent" => "bigint(21) NOT NULL",
			"titre" => "tinytext DEFAULT '' NOT NULL",
			"descriptif" => "tinytext DEFAULT '' NOT NULL",
 			"date" => "datetime NOT NULL default '0000-00-00 00:00:00",
			"maj"	=> "TIMESTAMP"
			);
	
	$cat_familles_key = array(
			"PRIMARY KEY"	=> "id_famille",
			);

	//-- Table cat_produits ------------------------------------------
	$cat_produits = array(
			"id_produit" => "bigint(21) NOT NULL",
			"titre" => "tinytext DEFAULT '' NOT NULL",
			"descriptif" => "tinytext DEFAULT '' NOT NULL",
			"prix_ht" => "decimal(6,2) default NULL",
 			"date" => "datetime NOT NULL default '0000-00-00 00:00:00",
			"maj"	=> "TIMESTAMP"
			);
	
	$cat_produits_key = array(
			"PRIMARY KEY"	=> "id_produit",
			);

	//-- Table cat_variantes ------------------------------------------
	$cat_variantes = array(
			"id_variante" => "bigint(21) NOT NULL",
			"id_produit" => "bigint(21) NOT NULL",
			"titre" => "tinytext DEFAULT '' NOT NULL",
			"descriptif" => "tinytext DEFAULT '' NOT NULL",
			"prix_ht" => "decimal(6,2) default NULL",
 			"date" => "datetime NOT NULL default '0000-00-00 00:00:00",
			"maj"	=> "TIMESTAMP"
			);
	
	$cat_variantes_key = array(
			"PRIMARY KEY"	=> "id_variante",
			);
	
	$tables_principales['spip_familles'] =
		array('field' => &$cat_familles, 'key' => &$cat_familles_key);
		
	$tables_principales['spip_produits'] =
		array('field' => &$cat_produits, 'key' => &$cat_produits_key);

	$tables_principales['spip_variantes'] =
		array('field' => &$cat_variantes, 'key' => &$cat_variantes_key);
				
	return $tables_principales;
}

?>