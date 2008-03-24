<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;
global $table_des_tables;

$GLOBALS['a2a_version_base'] = 0.1;


$spip_article_articles = array(
	"id_article"	=> "bigint(21) NOT NULL",
	"id_article_lie"	=> "bigint(21) NOT NULL"
	);
	
$spip_article_articles_key = array(
	"PRIMARY KEY"		=> "id_article, id_article_lie"
	);

$spip_article_articles_join = array(
	"id_article"		=> "id_article",
	"id_article_lie"		=> "id_article_lie"
	);

$tables_principales['spip_article_articles'] = array(
	'field' => &$spip_article_articles,
	'key' => &$spip_article_articles_key,
	'join' => &$spip_article_articles_join
	);

/*global $table_des_tables;
$table_des_tables['article_articles'] = 'articles_lies';

global $tables_jointures;
$tables_jointures['articles'][]= 'articles_lies';*/

?>
