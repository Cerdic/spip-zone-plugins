<?php
//
// Les tables : 
// 1 table descriptive des attributs
// 5 tables de liens attributs<->articles attributs<->rubriques attributs<->breves attributs<->sites et attributs<->auteurs

global $tables_principales;
global $tables_auxiliaires;

$spip_attributs = array(
	"id_attribut" 	=> "bigint(21) NOT NULL",
	"titre" 	=> "varchar(255) NOT NULL",
	"descriptif" 	=> "text NOT NULL",
	"texte" 	=> "text NOT NULL",
	"articles" 	=> "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
	"rubriques" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
	"breves" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
	"syndic" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
	"auteurs" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
	"groupes_mots" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
	"mots" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
	"redacteurs" 	=> "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_attributs_key = array(
	"PRIMARY KEY" => "id_attribut");

$tables_principales['spip_attributs'] = array(
	'field' => &$spip_attributs,
	'key' => &$spip_attributs_key);


$spip_attributs_articles = array(
	"id_attribut" 	=> "bigint(21) NOT NULL",
	"id_article" 	=> "bigint(21) NOT NULL");

$spip_attributs_articles_key = array(
	"KEY id_attribut" 	=> "id_attribut",
	"KEY id_article"	=> "id_article");

$tables_auxiliaires['spip_attributs_articles'] = array(
	'field' => &$spip_attributs_articles,
	'key' => &$spip_attributs_articles_key);


$spip_attributs_rubriques = array(
	"id_attribut" 	=> "bigint(21) NOT NULL",
	"id_rubrique" 	=> "bigint(21) NOT NULL");

$spip_attributs_rubriques_key = array(
	"KEY id_attribut" 	=> "id_attribut",
	"KEY id_rubrique" => "id_rubrique");

$tables_auxiliaires['spip_attributs_rubriques'] = array(
	'field' => &$spip_attributs_rubriques,
	'key' => &$spip_attributs_rubriques_key);


$spip_attributs_breves = array(
	"id_attribut" 	=> "bigint(21) NOT NULL",
	"id_breve" 	=> "bigint(21) NOT NULL");

$spip_attributs_breves_key = array(
	"KEY id_attribut" 	=> "id_attribut",
	"KEY id_breve" => "id_breve");

$tables_auxiliaires['spip_attributs_breves'] = array(
	'field' => &$spip_attributs_breves,
	'key' => &$spip_attributs_breves_key);


$spip_attributs_auteurs = array(
	"id_attribut" 	=> "bigint(21) NOT NULL",
	"id_auteur" 	=> "bigint(21) NOT NULL");

$spip_attributs_auteurs_key = array(
	"KEY id_attribut" 	=> "id_attribut",
	"KEY id_auteur" => "id_auteur");

$tables_auxiliaires['spip_attributs_auteurs'] = array(
	'field' => &$spip_attributs_auteurs,
	'key' => &$spip_attributs_auteurs_key);


$spip_attributs_syndic = array(
	"id_attribut" 	=> "bigint(21) NOT NULL",
	"id_syndic" 	=> "bigint(21) NOT NULL");

$spip_attributs_syndic_key = array(
	"KEY id_attribut" 	=> "id_attribut",
	"KEY id_syndic" => "id_syndic");

$tables_auxiliaires['spip_attributs_syndic'] = array(
	'field' => &$spip_attributs_syndic,
	'key' => &$spip_attributs_syndic_key);

$spip_attributs_mots = array(
	"id_attribut" 	=> "bigint(21) NOT NULL",
	"id_mot" 	=> "bigint(21) NOT NULL");

$spip_attributs_mots_key = array(
	"KEY id_attribut" 	=> "id_attribut",
	"KEY id_mot"	=> "id_mot");

$tables_auxiliaires['spip_attributs_mots'] = array(
	'field' => &$spip_attributs_mots,
	'key' => &$spip_attributs_mots_key);

$spip_attributs_groupes_mots = array(
	"id_attribut" 	=> "bigint(21) NOT NULL",
	"id_groupe" 	=> "bigint(21) NOT NULL");

$spip_attributs_groupes_mots_key = array(
	"KEY id_attribut" 	=> "id_attribut",
	"KEY id_groupe"	=> "id_groupe");

$tables_auxiliaires['spip_attributs_groupes_mots'] = array(
	'field' => &$spip_attributs_groupes_mots,
	'key' => &$spip_attributs_groupes_mots_key);

//-- Relations ----------------------------------------------------

global $tables_jointures;

$tables_jointures['spip_articles'][] = 'attributs_articles';
$tables_jointures['spip_articles'][] = 'attributs';
$tables_jointures['spip_attributs'][] = 'attributs_articles';

$tables_jointures['spip_rubriques'][] = 'attributs_rubriques';
$tables_jointures['spip_rubriques'][] = 'attributs';
$tables_jointures['spip_attributs'][] = 'attributs_rubriques';

$tables_jointures['spip_breves'][] = 'attributs_breves';
$tables_jointures['spip_breves'][] = 'attributs';
$tables_jointures['spip_attributs'][] = 'attributs_breves';

$tables_jointures['spip_auteurs'][] ='attributs_auteurs';
$tables_jointures['spip_auteurs'][] ='attributs';
$tables_jointures['spip_attributs'][] = 'attributs_auteurs';

$tables_jointures['spip_syndic'][] = 'attributs_syndic';
$tables_jointures['spip_syndic'][] = 'attributs';
$tables_jointures['spip_attributs'][] = 'attributs_syndic';

$tables_jointures['spip_mots'][] = 'attributs_mots';
$tables_jointures['spip_mots'][] = 'attributs';
$tables_jointures['spip_attributs'][] = 'attributs_mots';

$tables_jointures['spip_groupes_mots'][] = 'attributs_groupes_mots';
$tables_jointures['spip_groupes_mots'][] = 'attributs';
$tables_jointures['spip_attributs'][] = 'attributs_groupes_mots';

global $exceptions_des_jointures;
$exceptions_des_jointures['titre_attribut'] = array('spip_attributs', 'titre');

//-- Table des tables ----------------------------------------------------

global $table_des_tables;
$table_des_tables['attributs']='attributs';
$table_des_tables['attributs_articles']='attributs_articles';
$table_des_tables['attributs_rubriques']='attributs_rubriques';
$table_des_tables['attributs_breves']='attributs_breves';
$table_des_tables['attributs_auteurs']='attributs_auteurs';
$table_des_tables['attributs_syndic']='attributs_syndic';
$table_des_tables['attributs_mots']='attributs_mots';
$table_des_tables['attributs_groupes_mots']='attributs_groupes_mots';

?>