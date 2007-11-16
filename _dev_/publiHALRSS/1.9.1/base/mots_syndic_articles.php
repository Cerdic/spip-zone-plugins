<?php
//if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/serial');
include_spip('base/auxiliaires'); // pour eviter une reinit posterieure des tables modifiees

global $tables_auxiliaires;
global $tables_jointures;
global $table_des_tables;
//---------------------
$spip_mots_syndic_articles = array(
		"id_mot"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
		"id_syndic_article"	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_mots_syndic_articles_key = array(
		"PRIMARY KEY"	=> "id_syndic_article, id_mot",
		"KEY id_mot"	=> "id_mot");

$tables_auxiliaires['spip_mots_syndic_articles'] = array(
	'field' => &$spip_mots_syndic_articles,
	'key' => &$spip_mots_syndic_articles_key);
//-----------------------
//$tables_jointures['spip_syndic'][]= 'mots_syndic';
$tables_jointures['spip_syndic_articles'][]= 'mots_syndic_articles';
//$tables_jointures['spip_mots'][]= 'mots_syndic';
$tables_jointures['spip_mots'][]= 'mots_syndic_articles';
//$table_des_tables['mots_evenements']='mots_evenements';
$table_des_tables['mots_syndic_articles']='mots_syndic_articles';
// table syndic_articles pour les grpoues de mots 
//$tables_principales['spip_groupes_mots']['field']["syndic_articles"]= "varchar(3) NOT NULL";
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
if (isset($GLOBALS['meta']['publiHAL_base_mots_syndic_articles']))
	$spip_groupes_mots = array_merge($spip_groupes_mots,array("syndic_articles"	=> "varchar(3) NOT NULL"));
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