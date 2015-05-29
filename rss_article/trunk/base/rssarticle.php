<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajouter la table spip_articles_syndic
 * @param array $tables_auxiliaires
 * @return array
 */
function rssarticle_declarer_tables_auxiliaires($tables_auxiliaires){

	$spip_rssarticle = array(
			"id_article"  => "bigint(21) NOT NULL",
			"id_syndic" 	=> "bigint(21) NOT NULL");

	$spip_rssarticle_key = array(
			"INDEX" 	=> "id_article");
	
	$tables_auxiliaires['spip_articles_syndic'] = array(
			'field' => &$spip_rssarticle,
			'key' => &$spip_rssarticle_key);

	return $tables_auxiliaires;
}

/**
 * Declarer la table spip_articles_syndic dans les jointures
 * @param array $interface
 * @return array
 */
function rssarticle_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['articles_syndic']='articles_syndic';

	// permet au compilateur de determiner explicitement les jointures possibles
	// lorsqu'une boucle sur une table demande un champ inconnu
	$interface['tables_jointures']['spip_articles'][] = 'articles_syndic';

	return $interface;
}

/**
 * Ajouter des champs a la table syndic
 * @param array $tables_principales
 * @return array
 */
function rssarticle_declarer_tables_principales($tables_principales){
	// Extension de la table syndic
	$tables_principales['spip_syndic']['field']['rssarticle'] = "varchar(3) DEFAULT 'non' NOT NULL"; 

	return $tables_principales;
}


?>
