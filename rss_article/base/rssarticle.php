<?php
	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;

	$spip_rssarticle = array(
	  	"id_article"  => "bigint(21) NOT NULL",
	  	"id_syndic" 	=> "bigint(21) NOT NULL");
  
 	$spip_rssarticle_key = array(
		"INDEX" 	=> "id_article");

 	$tables_principales['spip_articles_syndic'] = array(
	  	'field' => &$spip_rssarticle,
	  	'key' => &$spip_rssarticle_key);

	$table_des_tables['articles_syndic'] = 'articles_syndic';
?>