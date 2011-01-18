<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

// xxx_declarer_tables_interfaces est l'endroit ou l'on indique les raccourcis à utiliser comme table de boucle SPIP
function catalogue_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['cat_variantes'] = 'cat_variantes';
	$interface['table_des_tables']['cat_options'] = 'cat_options';
	$interface['table_des_tables']['cat_options_articles'] = 'cat_options_articles';
	$interface['table_des_tables']['cat_transactions'] = 'cat_transactions';
	$interface['table_des_tables']['cat_lignes_transactions'] = 'cat_lignes_transactions';
	
	/**
	 * Objectif : pouvoir utiliser les champs liés dans les boucles...
	 *
	 */
	$interface['tables_jointures']['spip_articles'][]= 'cat_variantes';
	$interface['tables_jointures']['spip_cat_variantes'][]= 'articles';
	$interface['tables_jointures']['spip_cat_options']['id_cat_option']= 'spip_cat_options_articles';
	$interface['tables_jointures']['spip_articles']['id_article']= 'spip_cat_options_articles';

	
	/**
	 * Objectif : autoriser les traitements SPIP sur certains champs texte...
	 *
	 */
	$interface['table_des_traitements']['STATUT']['cat_variantes'] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['STATUT']['cat_options'] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['DESCRIPTIF']['cat_variantes'] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['DESCRIPTIF']['cat_options'] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['DESCRIPTIF']['cat_transactions'] = _TRAITEMENT_RACCOURCIS;

	return $interface;
}


function catalogue_declarer_tables_principales($tables_principales){

	//-- Table variantes ------------------------------------------
	$cat_variantes = array(
		"id_cat_variante" 	=> "bigint(21) NOT NULL auto_increment",
		"id_article" 	=> "bigint(21) NOT NULL DEFAULT 0",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "text DEFAULT '' NOT NULL",
		"statut"		=> "VARCHAR(10) NOT NULL DEFAULT 0",
		"prix_ht" 		=> "decimal(6,2) default NULL",
		"tva"			=> "decimal(4,3) default '0.196'",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"date_redac" 	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"maj"			=> "TIMESTAMP"
		);
	$cat_variantes_key = array(
		"PRIMARY KEY"	=> "id_cat_variante",
		"KEY id_article" => "id_article"
		);
	$tables_principales['spip_cat_variantes'] =
		array('field' => &$cat_variantes,'key' => &$cat_variantes_key,'join' => array('id_cat_variante'=>'id_cat_variante','id_article'=>'id_article'));


	//-- Table options ------------------------------------------
	$cat_options = array(
		"id_cat_option" 	=> "bigint(21) NOT NULL auto_increment",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "text DEFAULT '' NOT NULL",
		"statut"		=> "VARCHAR(10) NOT NULL DEFAULT 0",
		"prix_ht" 		=> "decimal(6,2) default 0",
		"tva"			=> "decimal(4,3) default '0.196'",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"maj"			=> "TIMESTAMP"
		);
	$cat_options_key = array(
		"PRIMARY KEY"	=> "id_cat_option"
		);
	$tables_principales['spip_cat_options'] =
		array('field' => &$cat_options, 'key' => &$cat_options_key);


	return $tables_principales;

}

function catalogue_declarer_tables_auxiliaires($tables_auxiliaires){

	//-- Table options_articles ------------------------------------------
	$cat_options_articles = array(
		"id_cat_option" => "bigint(21) NOT NULL DEFAULT 0",
		"id_article" 	=> "bigint(21) NOT NULL DEFAULT 0",
		);
	$cat_options_articles_key = array(
		"PRIMARY KEY"	=> "id_cat_option, id_article"
		);
	$tables_principales['spip_cat_options_articles'] =
		array('field' => &$cat_options_articles, 'key' => &$cat_options_articles_key);

	
	return $tables_auxiliaires;
}

?>
