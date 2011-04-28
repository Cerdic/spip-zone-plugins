<?php
global $table_des_tables; 
global $tables_principales; 
global $tables_auxiliaires; 
global $tables_jointures;

$spip_rssarticle = array( 
	 	                "id_article"  => "bigint(21) NOT NULL", 
	 	                "id_syndic"     => "bigint(21) NOT NULL"); 
	 	   
$spip_rssarticle_key = array( 
	 	                "INDEX"         => "id_article");

$tables_principales['spip_articles_syndic'] = array( 
	 	                'field' => &$spip_rssarticle, 
	 	                'key' => &$spip_rssarticle_key); 

$table_des_tables['articles_syndic'] = 'articles_syndic';                       

if (!defined("_ECRIRE_INC_VERSION")) return;

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