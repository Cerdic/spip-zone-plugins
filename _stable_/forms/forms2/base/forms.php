<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

//
// Formulaires : tables principales
//
global $tables_principales;

$spip_forms = array(
	"id_form" 	=> "bigint(21) NOT NULL",
	"titre" 	=> "varchar(255) NOT NULL",
	"descriptif" 	=> "text",
	"sondage" 	=> "varchar(255) NOT NULL",
	"structure" 	=> "text",
	"email" => "text",
	"champconfirm" => "varchar(255) NOT NULL",
	"texte" 	=> "text",
	"maj" 		=> "TIMESTAMP");

$spip_forms_key = array(
	"PRIMARY KEY" => "id_form");

$spip_reponses = array(
	"id_reponse" 	=> "bigint(21) NOT NULL",
	"id_form" 	=> "bigint(21) NOT NULL",
	"date"		=> "DATETIME NOT NULL",
	"ip"		=> "VARCHAR(255) NOT NULL",
	"id_auteur"	=> "bigint(21) NOT NULL",
	"id_article_export"	=> "bigint(21) NOT NULL",
	"url" => "VARCHAR(255) NOT NULL",
	"statut" 	=> "VARCHAR(255) NOT NULL",
	"cookie"	=> "VARCHAR(255) NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_reponses_key = array(
	"PRIMARY KEY" 	=> "id_reponse",
	"KEY id_form" 	=> "id_form, date",
	"KEY date" 	=> "date",
	"KEY cookie" 	=> "cookie",
	"KEY id_auteur" => "id_auteur",
	"KEY statut" 	=> "statut, id_form");


$tables_principales['spip_forms'] = array(
	'field' => &$spip_forms,
	'key' => &$spip_forms_key);

$tables_principales['spip_reponses'] = array(
	'field' => &$spip_reponses,
	'key' => &$spip_reponses_key);

// Boucle FORMS_CHAMPS
$formschamp_field = array(
		"id_form"	=> "bigint(21) NOT NULL",
		"champ" => "varchar(100)",
		"rang" => "bigint(21) NOT NULL",
		"titre" => "text",
		"type" => "varchar(100)",
		"obligatoire" => "varchar(3)",
		"extra_info" => "text", // une info supplementaire par champ a utiliser librement ex : type mot->id_groupe, type url->verif, type fichier -> taille
);
$formschamp_key = array(
	"PRIMARY KEY"	=> "id_form, champ",
	"KEY" => "rang"
);

$tables_principales['spip_forms_champs'] =
	array('field' => &$formschamp_field, 'key' => &$formschamp_key);

// Boucle FORMS_CHAMPS_CHOIX
$formschampchoix_field = array(
		"id_form"	=> "bigint(21) NOT NULL",
		"champ" => "varchar(100)",
		"choix" => "varchar(100) NOT NULL DEFAULT ''",
		"titre" => "text",
		"rang" => "bigint(21) NOT NULL"
);
$formschampchoix_key = array(
	"PRIMARY KEY"	=> "id_form, champ, choix",
	"KEY" => "choix"
);

$tables_principales['spip_forms_champs_choix'] =
	array('field' => &$formschampchoix_field, 'key' => &$formschampchoix_key);

//
// Formulaires : tables auxiliaires
//

$spip_reponses_champs = array(
	"id_reponse" 	=> "bigint(21) NOT NULL",
	"champ" 	=> "varchar(255) NOT NULL",
	"valeur" 	=> "BLOB NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_reponses_champs_key = array(
	"KEY champ" 	=> "champ, id_reponse",
	"KEY id_reponse" => "id_reponse");

$tables_principales['spip_reponses_champs'] = array(
	'field' => &$spip_reponses_champs,
	'key' => &$spip_reponses_champs_key);

$spip_forms_articles = array(
	"id_form" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_article" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_forms_articles_key = array(
	"KEY id_form" 	=> "id_form",
	"KEY id_article" => "id_article");

$tables_principales['spip_forms_articles'] = array(
	'field' => &$spip_forms_articles,
	'key' => &$spip_forms_articles_key);

//-- Relations ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_articles'][] = 'forms_articles';
$tables_jointures['spip_forms'][] = 'forms_articles';

global $table_des_tables;
$table_des_tables['forms']='forms';
$table_des_tables['reponses']='reponses';
$table_des_tables['reponses_champs']='reponses_champs';
$table_des_tables['forms_articles']='forms_articles';
$table_des_tables['forms_champs'] = 'forms_champs';
$table_des_tables['forms_champs_choix'] = 'forms_champs_choix';
?>