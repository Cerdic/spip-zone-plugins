<?php
// base/silospip_tables.php

/*
	Appele 
	- pour l'init de la base
	- par les exec en charge d'interpreter un patron  (?)
	- par l'espace public
	
	Ne pas oublier de faire l'include dans {PLUGIN}_mes_fonctions.php
	
*/

include_spip('inc/silospip_api_globales');

//////////////////////////////////
// Ici on declare la structure des tables au compilo
// Inspire de spip-listes, BoOz

	global $table_des_tables
	, $tables_principales
	, $tables_auxiliaires
	, $tables_jointures
	;

/* 
	//creer la table auteurs_elargis si besoin
	if(!is_array($tables_principales['spip_auteurs_elargis'])) {
		$spip_auteurs_elargis['id'] = "bigint(21) NOT NULL";
		$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
		$spip_auteurs_elargis['`spip_listes_format`'] = "VARCHAR( 8 ) DEFAULT 'non' NOT NULL";
		$spip_auteurs_elargis_key = array("PRIMARY KEY"	=> "id", 'KEY id_auteur' => 'id_auteur');
		$tables_principales['spip_auteurs_elargis']  =	array('field' => &$spip_auteurs_elargis, 'key' => &$spip_auteurs_elargis_key);
		
	}
*/
	
	
	$table_des_tables['silosites'] = 'silosites';

	$spip_silosites = array(
						"id_site"		=> "bigint(21) NOT NULL",
						"nom"			=> "text NOT NULL",
						"domaine"		=> "text NOT NULL",
						"titre"			=> "text NOT NULL",
						"descriptif"		=> "text NOT NULL",
						"date"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"id_createur"		=> "bigint(21) NOT NULL",
						"lang"			=> "varchar(10) NOT NULL",
						"maj"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"statut"		=> "varchar(10) NOT NULL"
					);
	$spip_silosites_key = array(
						"PRIMARY KEY"		=> "id_site",
						"KEY id_createur"	=> "id_createur"
					);

	$tables_principales['spip_silosites'] =
		array('field' => &$spip_silosites, 'key' => &$spip_silosites_key);
	
        $spip_auteurs_sites = array(
                                                "id_auteur"                     => "bigint(21) NOT NULL default '0'",
                                                "id_site"                      => "bigint(21) NOT NULL default '0'",
                                                "date_creation"      => "datetime NOT NULL default '0000-00-00 00:00:00'"
					);

        $spip_auteurs_sites_key = array(
                                                "PRIMARY KEY" => "id_auteur, id_site"
                                        );

      $tables_auxiliaires['spip_auteurs_sites'] = 
                array('field' => &$spip_auteurs_sites, 'key' => &$spip_auteurs_sites_key);


	$tables_jointures['spip_silosites'][]= 'auteurs';
	
	
?>
