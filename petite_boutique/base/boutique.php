<?php

function boutique_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['produits'] = 'produits';	
	$interface['table_des_traitements']['NOM']['produits'] = _TRAITEMENT_TYPO; // corrections de francais
	$interface['table_des_traitements']['TEXTE']['produits'] = _TRAITEMENT_RACCOURCIS; // + raccourcis spip
	$interface['table_des_tables']['avis_boutique'] = 'avis_boutique';	
	$interface['table_des_traitements']['TEXTE_AVIS']['avis_boutique'] = _TRAITEMENT_TYPO; 
	$interface['table_des_traitements']['TEXTE_AVIS']['avis_boutique'] = _TRAITEMENT_RACCOURCIS; 
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
//Table forum du produit			
$avis_boutique = array(
						"id_avis" => "BIGINT(21) NOT NULL",
						"id_produit" => "TINYTEXT NOT NULL",
						"id_auth" => "INT(11) NOT NULL ",
						"nom" => "TINYTEXT NOT NULL",
						"prenom" => "TINYTEXT NOT NULL",
						"email" => "TINYTEXT NOT NULL",
						"titre_avis"  => "TINYTEXT NOT NULL",
						"texte_avis" => "TEXT NOT NULL",
						"note" => "INT(11)",
						"date" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL  "
						);
$avis_boutique_key = array(
						"PRIMARY KEY" => "id_avis");	
						
	$tables_principales['spip_produits'] =
		array('field' => &$produits, 'key' => &$produits_key);
	$tables_principales['spip_avis_boutique'] =
		array('field' => &$avis_boutique, 'key' => &$avis_boutique_key);
	return $tables_principales;
}
function boutique_declarer_tables_objets_surnoms($surnoms) {
	// Le type ['*'] correspond a la table nommee 
	$surnoms['produit'] = 'produits';
	
	return $surnoms;
}

?>
