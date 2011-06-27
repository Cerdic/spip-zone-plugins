<?php
/**
* Plugin Abonnement
*
* Copyright (c) 2009
* BoOz booz@rezo.net 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

function abonnement_declarer_tables_interfaces($interfaces){
	// alias
	$interfaces['table_des_tables']['abonnements'] = 'abonnements';
	$interfaces['table_des_tables']['auteurs_elargis_abonnements'] = 'auteurs_elargis_abonnements';
	$interfaces['table_des_tables']['auteurs_elargis_articles'] = 'auteurs_elargis_articles';
	// champs date
	$interfaces['table_date']['auteurs_elargis_abonnements']='date';
	$interfaces['table_date']['auteurs_elargis_articles']='date';
	// jointures
	$interfaces['exception_des_jointures']['id_auteur_elargi']=array('spip_auteurs_elargis','id_auteur');

	return $interfaces;
}

function abonnement_declarer_tables_principales($tables_principales){

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
							"id_auteur" 	=> "int(10) unsigned NOT NULL",
							"id_abonnement" 			=> "int(10) unsigned NOT NULL",
							"date" 	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"validite" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"montant" => "int(10) unsigned NOT NULL",
							"statut_paiement" 	=> "tinytext NOT NULL",
							"hash" 				=> "tinytext NOT NULL",
							"stade_relance" => "int(10) unsigned NOT NULL"
							);

	$spip_auteurs_elargis_abonnements_key = array(
							"KEY" => "id_auteur"
							);	

	$tables_principales['spip_auteurs_elargis_abonnements'] = array(
			'field' => &$spip_auteurs_elargis_abonnements, 
			'key' => &$spip_auteurs_elargis_abonnements_key);
			
	//table auteurs_elargis_articles
	$spip_auteurs_elargis_articles = array(
							"id_auteur_elargi" 	=> "int(10) unsigned NOT NULL",
							"id_article" 			=> "int(10) unsigned NOT NULL",
							"date" 				=> "timestamp(14) NOT NULL",
							"statut_paiement" 	=> "tinytext NOT NULL",
							"montant" => "int(10) unsigned NOT NULL",
							"hash" 				=> "tinytext NOT NULL"
							);

	$spip_auteurs_elargis_articles_key = array(
							"KEY" => "id_auteur_elargi"
							);	

	$tables_principales['spip_auteurs_elargis_articles'] = array(
			'field' => &$spip_auteurs_elargis_articles, 
			'key' => &$spip_auteurs_elargis_articles_key);

	
	return $tables_principales;
}

?>
