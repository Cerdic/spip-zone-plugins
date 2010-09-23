<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

// xxx_declarer_tables_interfaces est l'endroit ou l'on indique les raccourcis à utiliser comme table de boucle SPIP
function catalogue_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['variantes'] = 'variantes';
	$interface['table_des_tables']['options'] = 'options';
	$interface['table_des_tables']['options_articles'] = 'options_articles';
	$interface['table_des_tables']['transactions'] = 'transactions';
	$interface['table_des_tables']['lignes_transactions'] = 'lignes_transactions';
	
	/**
	 * Objectif : pouvoir utiliser les champs liés dans les boucles...
	 *
	 */
	$interface['tables_jointures']['spip_articles'][]= 'variantes';
	$interface['tables_jointures']['spip_variantes'][]= 'articles';
	$interface['tables_jointures']['spip_options']['id_option']= 'spip_options_articles';
	$interface['tables_jointures']['spip_articles']['id_article']= 'spip_options_articles';

	
	/**
	 * Objectif : autoriser les traitements SPIP sur certains champs texte...
	 *
	 */
	$interface['table_des_traitements']['TITRE'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['STATUT'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['DESCRIPTIF'][] = _TRAITEMENT_RACCOURCIS;

	return $interface;
}


function catalogue_declarer_tables_principales($tables_principales){

	//-- Table variantes ------------------------------------------
	$variantes = array(
		"id_variante" 	=> "bigint(21) NOT NULL auto_increment",
		"id_article" 	=> "bigint(21) NOT NULL DEFAULT 0",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "TEXT DEFAULT '' NOT NULL",
		"statut"		=> "VARCHAR(10) NOT NULL DEFAULT 0",
		"prix_ht" 		=> "decimal(6,2) default NULL",
		"tva"			=> "decimal(4,3) default '0.196'",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"date_redac" 	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"maj"			=> "TIMESTAMP"
		);
	$variantes_key = array(
		"PRIMARY KEY"	=> "id_variante",
		"KEY id_article" => "id_article"
		);
	$tables_principales['spip_variantes'] =
		array('field' => &$variantes,'key' => &$variantes_key,'join' => array('id_variante'=>'id_variante','id_article'=>'id_article'));


	//-- Table options ------------------------------------------
	$options = array(
		"id_option" 	=> "bigint(21) NOT NULL auto_increment",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "TEXT DEFAULT '' NOT NULL",
		"statut"		=> "VARCHAR(10) NOT NULL DEFAULT 0",
		"prix_ht" 		=> "decimal(6,2) default 0",
		"tva"			=> "decimal(4,3) default '0.196'",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"maj"			=> "TIMESTAMP"
		);
	$options_key = array(
		"PRIMARY KEY"	=> "id_option"
		);
	$tables_principales['spip_options'] =
		array('field' => &$options, 'key' => &$options_key);


	//-- Table options_articles ------------------------------------------
	$options_articles = array(
		"id_option" 	=> "bigint(21) NOT NULL DEFAULT 0",
		"id_article" 	=> "bigint(21) NOT NULL DEFAULT 0",
		);
	$options_articles_key = array(
		"PRIMARY KEY"	=> "id_option, id_article"
		);
	$tables_principales['spip_options_articles'] =
		array('field' => &$options_articles, 'key' => &$options_articles_key);
		

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
	$transactions = array(
		"id_transaction"=> "bigint(21) NOT NULL auto_increment",
		"id_contact"	=> "bigint(21) NOT NULL DEFAULT 0",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"descriptif" 	=> "tinytext DEFAULT '' NOT NULL",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		);
	$transactions_key = array(
		"PRIMARY KEY"	=> "id_transaction",
		"KEY"			=> "id_contact"
		);
	$tables_principales['spip_transactions'] =
		array('field' => &$transactions, 'key' => &$transactions_key);

	//-- Table lignes_transactions ------------------------------------------
	/** 
	 * Chaque transaction est composée d'une ou plusieurs lignes;
	 * chaque ligne comporte :
	 * - un id_variante (qui remontre à l'id_article)
	 * - une quantité (quantité de cette variante)
	 * - un prix unitaire HT (qui restera fixe pour cette ransaction, malgré les évolutions potentielles du montant de la variante)
	 * - un taux de tva applicable (pour cette transaction)
	 * exemples de ligne :
	 * - 1 inscription tarif "professeur" à 250€ HT, TVA 19.6 (si client en France)
	 * - 1 "voyage 8 jours" à 795€ + option "all inclusive" à 119€, pas de TVA
	 * - 3 tee-shirts bleus extra à 18€ HT pièce, TVA 19.6
	 */
	$lignes_transactions = array(
		"id_ligne"		=> "bigint(21) NOT NULL auto_increment",
		"id_transaction"	=> "bigint(21) NOT NULL DEFAULT 0",
		"id_objet"		=> "bigint(21) NOT NULL DEFAULT 0",
 		"objet"			=> "varchar(25) NOT NULL", // peut etre une variante ou une option
		"quantite"		=> "float default NULL",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL", // peut varier; on fait un cliché à l'intant t
		"prix_ht" 		=> "decimal(6,2) default 0",
		"tva"			=> "decimal(4,3) default '0.196'",
		"maj"			=> "TIMESTAMP"		
		);
	$lignes_transactions_key = array(
		"PRIMARY KEY"	=> "id_ligne",
		"KEY"			=> "id_transaction, id_objet, objet"
		);
	$tables_principales['spip_lignes_transactions'] =
		array('field' => &$lignes_transactions, 'key' => &$lignes_transactions_key);
	
	return $tables_principales;

}


?>
