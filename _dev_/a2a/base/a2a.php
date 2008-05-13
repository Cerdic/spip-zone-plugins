<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;
global $table_des_tables;

$GLOBALS['a2a_version_base'] = 0.2;


$spip_articles_lies = array(
	"id_article"	=> "bigint(21) NOT NULL",
	"id_article_lie"	=> "bigint(21) NOT NULL",
	"rang"	=> "bigint(21) NOT NULL"
	);
	
$spip_articles_lies_key = array(
	"PRIMARY KEY"		=> "id_article",
	"KEY id_article_lie" => "id_article_lie",
	"KEY rang" => "rang"

	);

$spip_articles_lies_join = array(
	"id_article"		=> "id_article",
	"id_article_lie"		=> "id_article_lie",
	"rang"		=> "rang"
	);

$tables_principales['spip_articles_lies'] = array(
	'field' => &$spip_articles_lies,
	'key' => &$spip_articles_lies_key,
	'join' => &$spip_articles_lies_join
	);

global $table_des_tables;
$table_des_tables['articles_lies'] = 'articles_lies';
/*
global $tables_jointures;
$tables_jointures['articles'][]= 'articles_lies';
*/
?>
