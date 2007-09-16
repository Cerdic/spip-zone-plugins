<?php
/**
* Plugin Abonnement
*
* Copyright (c) 2007
* BoOz booz@rezo.net 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

$table_des_tables['abonnements'] = 'abonnements';
$table_des_tables['auteurs_elargis_abonnements'] = 'auteurs_elargis_abonnements';
$table_des_tables['auteurs_elargis_articles'] = 'auteurs_elargis_articles';



//-- Table CATEGORIES COTISATION ------------------------------------------
$spip_abonnements = array(
						"id_abonnement" 	=> "int(10) unsigned NOT NULL auto_increment",
						"libelle" 			=> "text NOT NULL",
						"duree" 			=> "text NOT NULL",
						"periode" 			=> "text NOT NULL",
						"montant" 		=> "float NOT NULL default '0'",
						"commentaire" 	=> "text NOT NULL",
						"maj" 				=> "timestamp(14) NOT NULL"
						);

$spip_abonnements_key = array(
						"PRIMARY KEY" => "id_abonnement"
						);	

$tables_principales['spip_abonnements'] = array(
		'field' => &$spip_abonnements, 
		'key' => &$spip_abonnements_key);

//table auteurs_elargis_abonnements
$spip_auteurs_elargis_abonnements = array(
						"id_auteur_elargi" 	=> "int(10) unsigned NOT NULL",
						"id_abonnement" 			=> "int(10) unsigned NOT NULL",
						"date" 				=> "timestamp(14) NOT NULL"
						);

$spip_auteurs_elargis_abonnements_key = array(
						"KEY" => "id_auteur_elargi"
						);	

$tables_principales['`spip_auteurs_elargis_abonnements`'] = array(
		'field' => &$spip_auteurs_elargis_abonnements, 
		'key' => &$spip_auteurs_elargis_abonnements_key);
		
//table auteurs_elargis_articles
$spip_auteurs_elargis_articles = array(
						"id_auteur_elargi" 	=> "int(10) unsigned NOT NULL",
						"id_article" 			=> "int(10) unsigned NOT NULL",
						"date" 				=> "timestamp(14) NOT NULL",
						"statut_paiement" 	=> "tinytext NOT NULL",
						"hash" 				=> "tinytext NOT NULL"
						);

$spip_auteurs_elargis_articles_key = array(
						"KEY" => "id_auteur_elargi"
						);	

$tables_principales['`spip_auteurs_elargis_articles`'] = array(
		'field' => &$spip_auteurs_elargis_articles, 
		'key' => &$spip_auteurs_elargis_articles_key);
		

?>