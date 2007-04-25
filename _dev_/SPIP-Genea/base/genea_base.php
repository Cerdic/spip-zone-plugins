<?php
/*	*********************************************************************
	*
	* Copyright (c) 2006
	* Xavier Burot
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global	$tables_principales,
		$tables_auxiliaires,
		$tables_jointures,
		$table_des_tables,
		$table_primary,
		$tables_relations,
		$exceptions_des_tables,
		$table_date,
		$table_des_traitements,
		$genea_version;
		
// -- Definition de la table genea --------------------------------------
$spip_genea =  array(
     "id_genea" => 	"BIGINT(21) NOT NULL AUTO_INCREMENT",
     "id_rubrique" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_key = array(
	"PRIMARY KEY" => "id_genea",
	"KEY ig_rubrique" => "id_rubrique");
	
$tables_principales['spip_genea'] = array(
	'field' => &$spip_genea,
	'key' => &$spip_genea_key);

$table_des_tables['genea'] = "genea";
$table_primary['genea'] = "id_genea";

// -- Definition de la table individu -----------------------------------
$spip_genea_individus = array(
     "id_individu" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
     "nom" => "TEXT NOT NULL",
     "prenoms" => "TEXT NOT NULL",
     "surnom" => "TEXT NOT NULL",
     "sexe" => "ENUM('', 'M', 'F') DEFAULT '' NOT NULL",
     "id_civilite" => "BIGINT(21) DEFAULT '0' NOT NULL",
     "id_genea" => "BIGINT(21) DEFAULT '0' NOT NULL",
     "id_famille" => "BIGINT(21) DEFAULT '0' NOT NULL",
     "date" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
     "maj" => "TIMESTAMP",
     "date_modif" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
     "auteur_modif" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_individus_key = array(
     "PRIMARY KEY" => "id_individu",
     "KEY nom" => "nom",
     "KEY id_genea" => "id_genea",
     "KEY id_famille" => "id_famille",
     "KEY date_modif" => "date_modif");

$tables_principales['spip_genea_individus'] = array(
	'field' => &$spip_genea_individus,
	'key' => &$spip_genea_individus_key);

$table_des_tables['individus'] = "genea_individus";
$table_primary['individus'] = "id_individu";

$table_date['individus']='date';

$tables_relations['rubriques']['id_genea'] = 'spip_genea';
$tables_relations['individus']['id_rubrique'] = 'spip_genea';

// -- Definition de la table d'evenements -------------------------------
$spip_genea_evt = array(
	"id_genea_evt" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
	"type_evt" => "VARCHAR(4) NOT NULL",
	"date" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"date_fin" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"descriptif" => "TEXT NOT NULL",
	"lieu" => "TEXT NOT NULL",
	"note" => "TEXT NOT NULL",
	"maj" => "TIMESTAMP");

$spip_genea_evt_key = array(
     "PRIMARY KEY" => "id_genea_evt",
     "KEY date" => "date",
     "KEY date_fin" => "date_fin");

$tables_principales['spip_genea_evt'] = array(
	'field' => &$spip_genea_evt,
	'key' => &$spip_genea_evt_key);

$table_des_tables['genea_evt'] = "genea_evt";
$table_primary['genea_evt'] = "id_genea_evt";

// -- Definition des relations entre tables INDIVIDUS et EVENEMENTS -----
$spip_genea_evt_individus = array(
	"id_genea_evt" => "BIGINT(21) DEFAULT '0' NOT NULL",
	"id_individu" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_evt_individus_key = array(
	"KEY id_individu" => "id_individu",
	"KEY id_genea_evt" => "id_genea_evt");

$table_des_tables['genea_evt_individus'] = "genea_evt_individus";

$tables_principales['spip_genea_evt_individus'] = array(
	'field' => &$spip_genea_evt_individus,
	'key' => &$spip_genea_evt_individus_key);

$tables_relations['genea_individus']['id_genea_evt'] = 'genea_evt_individus';
$tables_relations['genea_evt']['id_individu'] = 'genea_evt_individus';

$tables_jointures['spip_genea_individus'][] = "genea_evt_individus";
$tables_jointures['spip_genea_evt'][] = "genea_evt_individus";

// -- Definition de la table genea --------------------------------------
$spip_genea_sosa =  array(
     "id_sosa" => 	"BIGINT(21) NOT NULL AUTO_INCREMENT",
     "id_individu" => "BIGINT(21) DEFAULT '0' NOT NULL");

$spip_genea_sosa_key = array(
	"PRIMARY KEY" => "id_sosa",
	"KEY ig_rubrique" => "id_individu");
	
$tables_principales['spip_genea_sosa'] = array(
	'field' => &$spip_genea_sosa,
	'key' => &$spip_genea_sosa_key);

$table_des_tables['genea_sosa'] = "genea_sosa";
$table_primary['genea_sosa'] = "id_sosa";

// -- Definition des parametres de traitement ---------------------------
$table_des_traitements['DATE_FIN'][] = 'vider_date(%s)';
$table_des_traitements['PRENOMS'][] = 'typo(%s)';
$table_des_traitements['SURNOM'][] = 'typo(%s)';
$table_des_traitements['LIEU'][] = 'propre(%s)';
?>