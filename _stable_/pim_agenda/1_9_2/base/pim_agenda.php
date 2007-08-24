<?php

/*
 * P.I.M Agenda
 * Gestion d'un agenda collaboratif
 *
 * Auteur :
 * Cedric Morin, Notre-ville.net
 * (c) 2005,2007 - Distribue sous licence GNU/GPL
 *
 */
// -----------------------------------------------------------------------------
// Declaration des tables evenements
// creation 11/03/2006 pour SPIP 1.9

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

//-- Table PIM_AGENDA ------------------------------------------
$spip_pim_agenda = array(
		"id_agenda"	=> "bigint(21) NOT NULL",
		"type" => "ENUM('reunion','rendez-vous','evenement','anniversaire','rappel') DEFAULT 'reunion' NOT NULL",
		"prive"		=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
		"crayon"	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
		"id_article"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"date_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"lieu"	=> "text NOT NULL",
		"id_agenda_source"	=> "bigint(21) NOT NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$spip_pim_agenda_key = array(
		"PRIMARY KEY"	=> "id_agenda",
		"KEY date_debut"	=> "date_debut",
		"KEY date_fin"	=> "date_fin",
		"KEY id_article"	=> "id_article"
		);

$tables_principales['spip_pim_agenda'] =
	array('field' => &$spip_pim_agenda, 'key' => &$spip_pim_agenda_key);


//-- Table de relations MOTS_PIM_AGENDA ----------------------
$spip_mots_pim_agenda = array(
		"id_mot"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
		"id_agenda"	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_mots_pim_agenda_key = array(
		"PRIMARY KEY"	=> "id_mot, id_agenda",
		"KEY id_agenda"	=> "id_agenda");

$tables_auxiliaires['spip_mots_pim_agenda'] = array(
	'field' => &$spip_mots_pim_agenda,
	'key' => &$spip_mots_pim_agenda_key);

//-- Table de relations PIM_AGENDA_AUTEURS----------------------
$tables_principales['spip_pim_agenda_auteurs']['field'] = array (
"id_agenda" => "bigint(21) DEFAULT '0' NOT NULL",
"id_auteur" => "bigint(21) DEFAULT '0' NOT NULL"
);

$tables_principales['spip_pim_agenda_auteurs']['key'] = array (
"KEY id_agenda" => "id_agenda",
"KEY id_auteur" => "id_auteur"
);

//-- Table de relations PIM_AGENDA_INVITES----------------------
$tables_principales['spip_pim_agenda_invites']['field'] = array (
"id_agenda" => "bigint(21) DEFAULT '0' NOT NULL",
"id_auteur" => "bigint(21) DEFAULT '0' NOT NULL"
);

$tables_principales['spip_pim_agenda_invites']['key'] = array (
"KEY id_agenda" => "id_agenda",
"KEY id_auteur" => "id_auteur"
);

//-- Table de relations PIM_AGENDA_PUBLIES ----------------------
/*$tables_principales['spip_pim_agenda_publies']['field'] = array (
"id_publie" => "bigint(21) DEFAULT '0' NOT NULL",
"id_accede" => "bigint(21) DEFAULT '0' NOT NULL"
);

$tables_principales['spip_pim_agenda_publies']['key'] = array (
"KEY id_publie" => "id_publie",
"KEY id_accede" => "id_accede"
);*/	
	
$spip_groupes = array(
	"id_groupe" 	=> "bigint(21) NOT NULL",
	"titre" 	=> "varchar(255) NOT NULL",
	"descriptif" 	=> "text NOT NULL",
	"publique" 	=> "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
	"privee" 	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_groupes_key = array(
	"PRIMARY KEY" => "id_groupe");

$tables_principales['spip_groupes'] = array(
	'field' => &$spip_groupes,
	'key' => &$spip_groupes_key);

$spip_auteurs_groupes = array(
	"id_groupe" 	=> "bigint(21) NOT NULL",
	"id_auteur" 	=> "bigint(21) NOT NULL");

$spip_auteurs_groupes_key = array(
	"KEY id_groupe" 	=> "id_groupe",
	"KEY id_auteur" => "id_auteur");

$tables_auxiliaires['spip_auteurs_groupes'] = array(
	'field' => &$spip_auteurs_groupes,
	'key' => &$spip_auteurs_groupes_key);

//-- Jointures ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_articles'][]= 'pim_agenda';
$tables_jointures['spip_pim_agenda'][] = 'articles';
$tables_jointures['spip_mots'][]= 'mots_pim_agenda';
$tables_jointures['spip_pim_agenda'][] = 'mots_pim_agenda';
$tables_jointures['spip_auteurs'][] = 'auteurs_groupes';
$tables_jointures['spip_groupes']['id_auteur'] = 'auteurs_groupes';

global $exceptions_des_tables;
$exceptions_des_tables['pim_agenda']['id_rubrique']=array('spip_articles', 'id_rubrique');

global $table_primary;
$table_primary['pim_agenda']="id_agenda";

global $table_date;
$table_date['pim_agenda'] = 'date_debut';
// si on declare les tables dans $table_des_tables, il faut mettre le prefixe

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['pim_agenda']='pim_agenda';
$table_des_tables['mots_pim_agenda']='mots_pim_agenda';
$table_des_tables['pim_agenda_auteurs']='pim_agenda_auteurs';
$table_des_tables['pim_agenda_invites']='pim_agenda_invites';
$table_des_tables['groupes']='groupes';
$table_des_tables['auteurs_groupes']='auteurs_groupes';


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
		"syndic"	=> "varchar(3) NOT NULL");
if (defined('_DIR_PLUGIN_AGENDA'))
	$spip_groupes_mots = array_merge($spip_groupes_mots,array("evenements"	=> "varchar(3) NOT NULL"));
if (defined('_DIR_PLUGIN_PIMAGENDA'))
	$spip_groupes_mots = array_merge($spip_groupes_mots,array("pim_agenda"	=> "varchar(3) NOT NULL"));
$spip_groupes_mots = array_merge($spip_groupes_mots,array(
		"minirezo"	=> "varchar(3) NOT NULL",
		"comite"	=> "varchar(3) NOT NULL",
		"forum"	=> "varchar(3) NOT NULL",
		"maj"	=> "TIMESTAMP"));

$spip_groupes_mots_key = array(
		"PRIMARY KEY"	=> "id_groupe");

$tables_principales['spip_groupes_mots'] =
	array('field' => &$spip_groupes_mots, 'key' => &$spip_groupes_mots_key);


?>