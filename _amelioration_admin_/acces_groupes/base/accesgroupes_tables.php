<?php

/* OLD : v0.7
// fichier de config pour utiliser un préfixe pour les tables SPIP et/ou accesgroupes_xxx pour la gestion des groupes

// pour ne pas avoir à configurer ce fichier on utilise le inc_version.php3 pour assurer la connexion à la base de données
	 include_once ("inc_version.php3");

		$prefix_tables_SPIP = $table_prefix;	 // $table_prefix définie dans ecrire/inc_version.php3 (qui appelle mes_options.php3 s'il existe)
		$prefix_tables_jpk = $table_prefix."_accesgroupes";		 // pour la version originale : $prefix_tables_jpk = "jpk"

		$Tspip_rubriques = $prefix_tables_SPIP."_rubriques";
		$Tspip_articles = $prefix_tables_SPIP."_articles";
		$Tspip_breves = $prefix_tables_SPIP."_breves";
		$Tspip_auteurs = $prefix_tables_SPIP."_auteurs";
		$Tspip_auteurs_rubriques = $prefix_tables_SPIP."_auteurs_rubriques";
		
		$Tjpk_groupes = $prefix_tables_jpk."_groupes";
		$Tjpk_groupes_auteurs = $prefix_tables_jpk."_auteurs";
		$Tjpk_groupes_acces = $prefix_tables_jpk."_acces";
		
		$Tspip_messages = $prefix_tables_SPIP."_messages";
		$Tspip_auteurs_messages = $prefix_tables_SPIP."_auteurs_messages";
*/

// définition des tables utilisées par accesgroupes
    global $tables_principales;
    global $tables_auxiliaires;
		
    $spip_accesgroupes_groupes = array(
          "id_grpacces" => "bigint(20) NOT NULL auto_increment",
          "nom" => "varchar(30) NOT NULL default ''",
          "description" => "varchar(250) default NULL",
          "actif" => "smallint(1) NOT NULL default '0'",
          "proprio" => "bigint(21) NOT NULL default '0'",
					"demande_acces" => "tinyint(4) NOT NULL default '0'"
    );
		$spip_accesgroupes_groupes_key = array(
          "PRIMARY KEY" => "id_grpacces",
          "UNIQUE KEY nom" => "nom"
		);
    $tables_principales['spip_accesgroupes_groupes'] = array(
    	'field' => &$spip_accesgroupes_groupes,
    	'key' => &$spip_accesgroupes_groupes_key
		 );
		
		$spip_accesgroupes_auteurs = array(
          "id_grpacces" => "bigint(21) NOT NULL default '0'",
          "id_auteur" => "bigint(21) NOT NULL default '0'",
          "id_ss_groupe" => "bigint(21) NOT NULL default '0'",
          "sp_statut" => "varchar(255) NOT NULL default ''",
          "dde_acces" => "smallint(1) NOT NULL default '1'",
          "proprio" => "bigint(21) NOT NULL default '0'"
		);
    $spip_accesgroupes_auteurs_key = array(
		      "UNIQUE KEY id_grp" => "id_grpacces,id_auteur,id_ss_groupe,sp_statut"
    );
    $tables_auxiliaires['spip_accesgroupes_auteurs'] = array(
    	'field' => &$spip_accesgroupes_auteurs,
    	'key' => &$spip_accesgroupes_auteurs_key
    );
		$spip_accesgroupes_acces = array(
          "id_grpacces" => "bigint(21) NOT NULL default '0'",
          "id_rubrique" => "bigint(21) NOT NULL default '0'",
          "id_article" => "bigint(21) default NULL",
          "dtdb" => "date default NULL",
          "dtfn" => "date default NULL",
          "proprio" => "bigint(21) NOT NULL default '0'",
					"prive_public" => "SMALLINT(6) NOT NULL default '0'"
		);
		$spip_accesgroupes_acces_key = array(
          "KEY id_grpacces" => "id_grpacces",
          "KEY id_rubrique" => "id_rubrique",
          "KEY id_article" => "id_article"
		);
    $tables_auxiliaires['spip_accesgroupes_acces'] = array(
    	'field' => &$spip_accesgroupes_acces,
    	'key' => &$spip_accesgroupes_acces_key
    );
		
// relations entre les tables
		global $tables_jointures;
		$tables_jointures['spip_auteurs'][] = 'accesgroupes_auteurs';
		$tables_jointures['spip_accesgroupes_groupes'][] = 'accesgroupes_auteurs';
		
		$tables_jointures['spip_rubriques'][] = 'accesgroupes_acces';
		$tables_jointures['spip_accesgroupes_groupes'][] = 'accesgroupes_acces';
		
// table des tables
	  global $table_des_tables;
		$table_des_tables['accesgroupes_groupes'] = 'accesgroupes_groupes';
		$table_des_tables['accesgroupes_acces'] = 'accesgroupes_acces';
		$table_des_tables['accesgroupes_auteurs'] = 'accesgroupes_auteurs';
		

/*		
global $tables_principales;
global $tables_auxiliaires;

$spip_zones = array(
	"id_zone" 	=> "bigint(21) NOT NULL",
	"titre" 	=> "varchar(255) NOT NULL",
	"descriptif" 	=> "text NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_zones_key = array(
	"PRIMARY KEY" => "id_zone");

$tables_principales['spip_zones'] = array(
	'field' => &$spip_zones,
	'key' => &$spip_zones_key);

$spip_zones_auteurs = array(
	"id_zone" 	=> "bigint(21) NOT NULL",
	"id_auteur" 	=> "bigint(21) NOT NULL");

$spip_zones_auteurs_key = array(
	"KEY id_zone" 	=> "id_zone",
	"KEY id_auteur" => "id_auteur");

$tables_auxiliaires['spip_zones_auteurs'] = array(
	'field' => &$spip_zones_auteurs,
	'key' => &$spip_zones_auteurs_key);

$spip_zones_rubriques = array(
	"id_zone" 	=> "bigint(21) NOT NULL",
	"id_rubrique" 	=> "bigint(21) NOT NULL");

$spip_zones_rubriques_key = array(
	"KEY id_zone" 	=> "id_zone",
	"KEY id_rubrique" => "id_rubrique");

$tables_auxiliaires['spip_zones_rubriques'] = array(
	'field' => &$spip_zones_rubriques,
	'key' => &$spip_zones_rubriques_key);

global $tables_jointures;
$tables_jointures['spip_auteurs'][] = 'zones_auteurs';
$tables_jointures['spip_zones'][] = 'zones_auteurs';

$tables_jointures['spip_rubriques'][] = 'zones_rubriques';
$tables_jointures['spip_zones'][] = 'zones_rubriques';

global $table_des_tables;
$table_des_tables['zones']='zones';
$table_des_tables['zones_rubriques']='zones_rubriques';
$table_des_tables['zones_auteurs']='zones_auteurs';
*/

		
		
?>