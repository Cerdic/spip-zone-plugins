<?php

function boutique_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['produits'] = 'produits';	
	$interface['table_des_traitements']['NOM']['produits'] = _TRAITEMENT_TYPO; // corrections de francais
	$interface['table_des_traitements']['TEXTE']['produits'] = _TRAITEMENT_RACCOURCIS; // + raccourcis spip
	return $interface;
}


function boutique_declarer_tables_principales($tables_principales){
	//-- Table PRODUITS ------------------------------------------
	$produits = array(
			"id_produit"	=> "bigint(21) NOT NULL",
			"nom"	=> "tinytext DEFAULT '' NOT NULL",
			"descriptif"	=> "tinytext DEFAULT '' NOT NULL",
			"texte"	=> "text DEFAULT '' NOT NULL",
			"prix"	=> "tinytext DEFAULT '' NOT NULL",
			"reference"	=> "tinytext DEFAULT '' NOT NULL",
			"rubrique"=> "tinytext DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP"
			);
	
	$produits_key = array(
			"PRIMARY KEY"	=> "id_produit",
			);
	
	$tables_principales['spip_produits'] =
		array('field' => &$produits, 'key' => &$produits_key);

	return $tables_principales;
}
function boutique_declarer_tables_objets_surnoms($surnoms) {
	// Le type ['*'] correspond a la table nommee 
	$surnoms['produit'] = 'produits';
	
	return $surnoms;
}

?>
