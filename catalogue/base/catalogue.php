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

	

	//-- Table transactions ------------------------------------------
	/**
	 * PARTIE EN COURS DE VALIDATION - Est-ce que les transactions ne devraient pas faire partie d'un plugin à part ?
	 * Une transaction est une opération, effectuée par une personne (id_contact), concernant un ou des objets (lignes_transaction)
	 * La personne est le id_contact; éventuellement le id_compte si la teransaction est effectuée au nom d'une personne morale
	 * Pour l'objet, il peut s'agir du N° d'article, du N° de variante, et du ou des N° d'options
	 * La transaction peut avoir différents statuts (en cours, validée, expédiée, traitée...)
	 * Elle a eu lieu à une certaine date
	 * elle pourrait etre extensible
	 */
	$cat_transactions = array(
		"id_cat_transaction"=> "bigint(21) NOT NULL auto_increment",
		// devrait etre id_auteur la-dessous...
		"id_contact"	=> "bigint(21) NOT NULL DEFAULT 0",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "tinytext DEFAULT '' NOT NULL",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		);
	$cat_transactions_key = array(
		"PRIMARY KEY"	=> "id_cat_transaction",
		"KEY"			=> "id_contact"
		);
	$tables_principales['spip_cat_transactions'] =
		array('field' => &$cat_transactions, 'key' => &$cat_transactions_key);

	//-- Table lignes_transactions ------------------------------------------
	/** 
	 * Chaque transaction est composée d'une ou plusieurs lignes;
	 * chaque ligne comporte :
	 * - un id_variante (qui remontre à l'id_article)
	 * - une quantité (quantité de cette variante)
	 * - un prix unitaire HT (qui restera fixe pour cette transaction, malgré les évolutions potentielles du montant de la variante)
	 * - un taux de tva applicable (pour cette transaction)
	 * exemples de ligne :
	 * - 1 inscription tarif "professeur" à 250€ HT, TVA 19.6 (si client en France)
	 * - 1 "voyage 8 jours" à 795€ + option "all inclusive" à 119€, pas de TVA
	 * - 3 tee-shirts bleus extra à 18€ HT pièce, TVA 19.6
	 */
	$cat_lignes_transactions = array(
		"id_cat_ligne"		=> "bigint(21) NOT NULL auto_increment",
		"id_cat_transaction"	=> "bigint(21) NOT NULL DEFAULT 0",
		"id_objet"		=> "bigint(21) NOT NULL DEFAULT 0",
 		"objet"			=> "varchar(25) NOT NULL", // peut etre une variante ou une option
		"quantite"		=> "float default NULL",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL", // peut varier; on fait un cliché à l'intant t
		"prix_ht" 		=> "decimal(6,2) default 0",
		"tva"			=> "decimal(4,3) default '0.196'",
		"maj"			=> "TIMESTAMP"		
		);
	$cat_lignes_transactions_key = array(
		"PRIMARY KEY"	=> "id_cat_ligne",
		"KEY"			=> "id_cat_transaction, id_objet, objet"
		);
	$tables_principales['spip_cat_lignes_transactions'] =
		array('field' => &$cat_lignes_transactions, 'key' => &$cat_lignes_transactions_key);
	
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
