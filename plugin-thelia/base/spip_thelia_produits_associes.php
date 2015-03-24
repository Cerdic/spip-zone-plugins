<?php
// -----------------------------------------------------------------------------
// Declaration des tables associatives : articles_produits et rubriques_produits
include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;


//-- Table produits_articles ------------------------------------------
$spip_produits_articles = array(
	"id_produit" => "bigint(21) NOT NULL",
	"id_article" => "bigint(21) NOT NULL"
);

$spip_produits_articles_key = array(
	"PRIMARY KEY" => "id_produit, id_article",
	"KEY id_article" => "id_article");


$tables_auxiliaires['spip_produits_articles'] = array(
	'field' => &$spip_produits_articles,
	'key' => &$spip_produits_articles_key);

global $tables_jointures;
$tables_jointures['spip_articles'][] = 'produits_articles';


// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['produits_articles'] = 'produits_articles';


//-- Table produits_rubriques ------------------------------------------
$spip_produits_rubriques = array(
	"id_produit" => "bigint(21) NOT NULL",
	"id_rubrique" => "bigint(21) NOT NULL"
);

$spip_produits_rubriques_key = array(
	"PRIMARY KEY" => "id_produit, id_rubrique",
	"KEY id_rubrique" => "id_rubrique");


$tables_auxiliaires['spip_produits_rubriques'] = array(
	'field' => &$spip_produits_rubriques,
	'key' => &$spip_produits_rubriques_key);

global $tables_jointures;
$tables_jointures['spip_rubriques'][] = 'produits_rubriques';


// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['produits_rubriques'] = 'produits_rubriques';

//-- Table rubriquesthelia_articles ------------------------------------------
$spip_rubriquesthelia_articles = array(
	"id_rubriquethelia" => "bigint(21) NOT NULL",
	"id_article" => "bigint(21) NOT NULL"
);

$spip_rubriquesthelia_articles_key = array(
	"PRIMARY KEY" => "id_rubriquethelia, id_article",
	"KEY id_article" => "id_article");


$tables_auxiliaires['spip_rubriquesthelia_articles'] = array(
	'field' => &$spip_rubriquesthelia_articles,
	'key' => &$spip_rubriquesthelia_articles_key);

global $tables_jointures;
$tables_jointures['spip_articles'][] = 'rubriquesthelia_articles';


// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['rubriquesthelia_articles'] = 'rubriquesthelia_articles';


//-- Table rubriquesthelia_rubriques ------------------------------------------
$spip_rubriquesthelia_rubriques = array(
	"id_rubriquethelia" => "bigint(21) NOT NULL",
	"id_rubrique" => "bigint(21) NOT NULL"
);

$spip_rubriquesthelia_rubriques_key = array(
	"PRIMARY KEY" => "id_rubriquethelia, id_rubrique",
	"KEY id_rubrique" => "id_rubrique");


$tables_auxiliaires['spip_rubriquesthelia_rubriques'] = array(
	'field' => &$spip_rubriquesthelia_rubriques,
	'key' => &$spip_rubriquesthelia_rubriques_key);

global $tables_jointures;
$tables_jointures['spip_rubriques'][] = 'rubriquesthelia_rubriques';


// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['rubriquesthelia_rubriques'] = 'rubriquesthelia_rubriques';
	
