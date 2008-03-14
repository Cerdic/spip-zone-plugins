<?php
/* *********************************************************************
   *
   * Copyright (c) 2007-2008
   * Xavier Burot
   * fichier : base/genea_base.php
   *
   * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
   *
   *********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return; // Securite

//include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

//
// Structure des tables
//
global
   $tables_principales,
   $tables_auxiliaires,
   $tables_jointures,
   $table_des_tables,
   $table_primary,
   $exceptions_des_tables,
   $table_date,
   $table_des_traitements,
   $table_prefix,
   $tables_genea;

// -- Numero de version de la base installee ----------------------------
$GLOBALS['version_base_genea'] = "1.01";

// -- Liste des differentes tables utilisees par le plugin --------------
$tables_genea = array(
   $table_prefix."_genea",
   $table_prefix."_genea_individus",
   $table_prefix."_genea_familles",
   $table_prefix."_genea_sosa",
   $table_prefix."_genea_evt",
   $table_prefix."_genea_sources",
   $table_prefix."_genea_doc_sources",
   $table_prefix."_genea_notes");

// -- Definition de la table genea --------------------------------------
$spip_genea =  array(
     "id_genea" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
     "id_rubrique" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_key = array(
   "PRIMARY KEY" => "id_genea, id_rubrique",
   "KEY id_rubrique" => "id_rubrique");

$tables_principales[$table_prefix.'_genea'] = array(
   'field' => &$spip_genea,
   'key' => &$spip_genea_key);

$table_des_tables['genea'] = "genea";
$table_primary['genea'] = "id_genea";

// -- Definition de la table individus -----------------------------------
$spip_genea_individus = array(
	"id_individu" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
	"patronyme" => "VARCHAR(255) NOT NULL",
	"prenoms" => "VARCHAR(255) NOT NULL",
	"surnom" => "VARCHAR(255) NOT NULL",
	"sexe" => "ENUM('i', 'h', 'f') DEFAULT 'i' NOT NULL",
	"civilite" => "VARCHAR(4) NOT NULL",
	"id_genea" => "BIGINT(21) DEFAULT '0' NOT NULL",
	"id_famille" => "BIGINT(21) DEFAULT '0' NOT NULL",
	"position" => "INT(10) DEFAULT '0' NOT NULL",
    "type_filiation" => "VARCHAR(4) NULL DEFAULT NULL",
	"date_creat" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");

$spip_genea_individus_key = array(
	"PRIMARY KEY" => "id_individu",
	"KEY patronyme" => "patronyme",
	"KEY id_genea" => "id_genea",
	"KEY id_pere" => "id_pere",
	"KEY id_mere" => "id_mere");

$tables_principales[$table_prefix.'_genea_individus'] = array(
   'field' => &$spip_genea_individus,
   'key' => &$spip_genea_individus_key);

$table_des_tables['genea_individus'] = "genea_individus";
$table_primary['genea_individus'] = "id_individu";

//$tables_jointures['rubriques'][] = 'genea';
//$tables_jointures['genea_individus'][] = 'genea';

// -- Définition de la table familles -----------------------------------
$spip_genea_familles = array (
	"id_famille" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
	"id_epoux" => "BIGINT(21) NULL DEFAULT NULL",
	"id_epouse" => "BIGINT(21) NULL DEFAULT NULL",
	"type_union" => "VARCHAR(4) NOT NULL",
	"nbre_enfants" => "INT(10) DEFAULT '0' NOT NULL",
	"id_genea" => "BIGINT(21) DEFAULT '0' NOT NULL",
	"date_creat" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");

$spip_genea_familles_key = array(
     "PRIMARY KEY" => "id_famille",
     "KEY id_epoux" => "id_epoux",
     "KEY id_epouse" => "id_epouse");

$tables_principales[$table_prefix.'_genea_familles'] = array(
   'field' => &$spip_genea_familles,
   'key' => &$spip_genea_familles_key);

$table_des_tables['genea_familles'] = "genea_familles";
$table_primary['genea_familles'] = "id_famille";

// -- Definition de la table de nurmerotation SOSA ----------------------
$spip_genea_sosa =  array(
     "id_sosa" =>    "BIGINT(21) DEFAULT '0' NOT NULL",
     "id_genea" => "BIGINT(21) DEFAULT '0' NOT NULL",
     "id_individu" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_sosa_key = array(
	"PRIMARY KEY" => "id_sosa",
	"KEY id_genea" => "id_genea",
	"KEY id_individu" => "id_individu");

$tables_principales[$table_prefix.'_genea_sosa'] = array(
	'field' => &$spip_genea_sosa,
	'key' => &$spip_genea_sosa_key);

$table_des_tables['genea_sosa'] = "genea_sosa";
$table_primary['genea_sosa'] = "id_sosa";

// -- Definition de la table d'evenements -------------------------------
$spip_genea_evt = array(
   "id_genea_evt" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
   "id_individu" => "BIGINT(21) DEFAULT '0' NOT NULL",
   "type_evt" => "VARCHAR(4) NOT NULL",
   "date_debut" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
   "date_debut2" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
   "date_fin" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
   "date_fin2" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
   "precision" => "ENUM('', 'v', 'a', 's', 'p') DEFAULT '' NOT NULL",
   "descriptif" => "TEXT NOT NULL",
   "id_lieu" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_evt_key = array(
     "PRIMARY KEY" => "id_genea_evt",
     "KEY date_debut" => "date_debut",
     "KEY date_fin" => "date_fin");

$tables_principales[$table_prefix.'_genea_evt'] = array(
   'field' => &$spip_genea_evt,
   'key' => &$spip_genea_evt_key);

$table_des_tables['genea_evt'] = "genea_evt";
$table_primary['genea_evt'] = "id_genea_evt";

// -- Definition de la table des sources --------------------------------
$spip_genea_sources = array(
   "id_source" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
   "descriptif" => "TEXT NOT NULL",
   "id_syndic" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_sources_key = array(
     "PRIMARY KEY" => "id_source",
     "KEY id_syndic" => "id_syndic");

$tables_principales[$table_prefix.'_genea_sources'] = array(
   'field' => &$spip_genea_sources,
   'key' => &$spip_genea_sources_key);

$table_des_tables['genea_sources'] = "genea_sources";
$table_primary['genea_sources'] = "id_source";

// -- Definition de la table de liaisons entre les documents et les sources
$spip_genea_doc_sources = array(
   "id_document" => "BIGINT(21) DEFAULT '0' NOT NULL",
   "id_individu" => "BIGINT(21) DEFAULT '0' NOT NULL",
   "id_genea_evt" => "BIGINT(21) DEFAULT '0' NOT NULL",
   "id_source" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_doc_sources_key = array(
     "PRIMARY KEY" => "id_individu, id_genea_evt, id_source, id_document",
     "KEY id_document" => "id_document");

$tables_principales[$table_prefix.'_genea_doc_sources'] = array(
   'field' => &$spip_genea_doc_sources,
   'key' => &$spip_genea_doc_sources_key);

$table_des_tables['genea_doc_sources'] = "genea_doc_sources";
$table_primary['genea_doc_sources'] = "id_document";

// -- Definition des parametres de traitement ---------------------------
//$table_des_traitements['DATE_DEBUT'][] = 'vider_date(%s)';
//$table_des_traitements['DATE_FIN'][] = 'vider_date(%s)';
$table_des_traitements['PATRONYME'][] = 'typo(%s)';
$table_des_traitements['PRENOMS'][] = 'typo(%s)';
$table_des_traitements['SURNOM'][] = 'typo(%s)';
$table_des_traitements['LIEU'][] = 'propre(%s)';
?>