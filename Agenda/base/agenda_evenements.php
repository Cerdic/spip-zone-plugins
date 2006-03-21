<?php
// -----------------------------------------------------------------------------
// Declaration des tables evenements
// creation 11/03/2006 pour SPIP 1.9

//include_spip('base/serial');

global $tables_principales;
global $tables_auxiliaires;

//-- Table EVENEMENTS ------------------------------------------
$evenements = array(
		"id_evenement"	=> "bigint(21) NOT NULL",
		"date_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"lieu"	=> "text NOT NULL",
		"horaire" => "ENUM('oui','non') DEFAULT 'oui' NOT NULL",
		"id_evenement_source"	=> "bigint(21) NOT NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$evenements_key = array(
		"PRIMARY KEY"	=> "id_evenement",
		"KEY date_debut"	=> "date_debut",
		"KEY date_fin"	=> "date_fin",
		);

$tables_principales['spip_evenements'] =
	array('field' => &$evenements, 'key' => &$evenements_key);

	
//-- Table de relations EVENEMENTS_ARTICLES----------------------
$spip_evenements_articles = array(
		"id_evenement"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
		"id_article"	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_evenements_articles_key = array(
		"PRIMARY KEY"	=> "id_evenement, id_article",
		"KEY id_evenement"	=> "id_evenement",
		"KEY id_article"	=> "id_article");

$tables_auxiliaires['spip_evenements_articles'] = array(
	'field' => &$spip_evenements_articles,
	'key' => &$spip_evenements_articles_key);

//-- Table de relations MOTS_EVENEMENTS----------------------
$spip_mots_evenements = array(
		"id_mot"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
		"id_evenement"	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_mots_evenements_key = array(
		"PRIMARY KEY"	=> "id_mot, id_evenement",
		"KEY id_mot"	=> "id_mot",
		"KEY id_evenement"	=> "id_evenement");

$tables_auxiliaires['spip_mots_evenements'] = array(
	'field' => &$spip_mots_evenements,
	'key' => &$spip_mots_evenements_key);

//-- Jointures ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_articles'][]= 'evenements_articles';
$tables_jointures['spip_evenements'][] = 'evenements_articles';
$tables_jointures['spip_mots'][]= 'mots_evenements';
$tables_jointures['spip_evenements'][] = 'mots_evenements';

global $table_primary;
$table_primary['evenements']="id_evenement";

global $table_date;
$table_date['evenements'] = 'date_debut';
// si on declare les tables dans $table_des_tables, il faut mettre le prefixe

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['evenements']='evenements';
$table_des_tables['evenements_articles']='evenements_articles';
$table_des_tables['mots_evenements']='mots_evenements';



// Extension de la table des groupes de mots cles
$spip_groupes_mots = array(
		"id_groupe"	=> "bigint(21) NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"texte"	=> "longblob NOT NULL",
		"unseul"	=> "varchar(3) NOT NULL",
		"obligatoire"	=> "varchar(3) NOT NULL",
		"articles"	=> "varchar(3) NOT NULL",
		"breves"	=> "varchar(3) NOT NULL",
		"rubriques"	=> "varchar(3) NOT NULL",
		"syndic"	=> "varchar(3) NOT NULL",
		"evenements"	=> "varchar(3) NOT NULL",
		"minirezo"	=> "varchar(3) NOT NULL",
		"comite"	=> "varchar(3) NOT NULL",
		"forum"	=> "varchar(3) NOT NULL",
		"maj"	=> "TIMESTAMP");

$spip_groupes_mots_key = array(
		"PRIMARY KEY"	=> "id_groupe");

$tables_principales['spip_groupes_mots'] =
	array('field' => &$spip_groupes_mots, 'key' => &$spip_groupes_mots_key);


?>