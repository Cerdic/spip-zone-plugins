<?php
/*
 * charts
 *
 * Auteur :
 * Cedric MORIN
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

$spip_charts = array(
	"id_chart" 	=> "bigint(21) NOT NULL",
	"titre" 	=> "varchar(255) NOT NULL",
	"descriptif" 	=> "text",
	"largeur"	=> "integer NOT NULL",
	"hauteur"	=> "integer NOT NULL",
	"code" 	=> "text",
	"background" 	=> "char(6) NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_charts_key = array(
	"PRIMARY KEY" => "id_chart");

global $tables_principales;
$tables_principales['spip_charts'] = array(
	'field' => &$spip_charts,
	'key' => &$spip_charts_key);

$spip_charts_articles = array(
	"id_chart" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_article" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_charts_articles_key = array(
	"KEY id_chart" 	=> "id_chart",
	"KEY id_article" => "id_article");

$tables_principales['spip_charts_articles'] = array(
	'field' => &$spip_charts_articles,
	'key' => &$spip_charts_articles_key);

//-- Relations ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_articles'][] = 'charts_articles';
$tables_jointures['spip_charts'][] = 'charts_articles';

global $table_des_tables;
$table_des_tables['charts']='charts';
$table_des_tables['charts_articles']='charts_articles';

?>