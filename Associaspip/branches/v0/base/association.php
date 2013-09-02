<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & François de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

// Declaration des tables evenements

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

//-- Table CATEGORIES COTISATION ------------------------------------------
$spip_asso_categories = array(
						"id_categorie" 	=> "int(10) unsigned NOT NULL auto_increment",
						"valeur" 			=> "tinytext NOT NULL",
						"libelle" 			=> "text NOT NULL",
						"duree" 			=> "text NOT NULL",
						"cotisation" 		=> "float NOT NULL default '0'",
						"commentaires" 	=> "text NOT NULL",
						"maj" 				=> "timestamp(14) NOT NULL"
						);

$spip_asso_categories_key = array(
						"PRIMARY KEY" => "id_categorie"
						);	

$tables_principales['spip_asso_categories'] = array(
		'field' => &$spip_asso_categories, 
		'key' => &$spip_asso_categories_key);
		
//-- Table PROFIL ASSOCIATION ------------------------------------------						
$spip_asso_profil = array(
						"id_profil" 	=> "BIGINT(21) NOT NULL AUTO_INCREMENT",
						"nom" 		=> "TINYTEXT NOT NULL",
						"numero" 		=> "TINYTEXT NOT NULL",
						"rue" 			=> "TINYTEXT NOT NULL",
						"cp" 			=> "TINYTEXT NOT NULL",
						"ville" 			=> "TINYTEXT NOT NULL",
						"telephone" 	=> "TINYTEXT",
						"siret" 		=> "TINYTEXT",
						"declaration" 	=> "TINYTEXT",
						"prefet" 		=> "TINYTEXT",
						"president" 	=> "TINYTEXT",
						"maj" 			=> "timestamp(14) NOT NULL",
						"mail"			=> "TINYTEXT NOT NULL",
						"dons" 		=> "TINYTEXT",
						"ventes" 		=> "TINYTEXT",
						"comptes" 	=> "TINYTEXT",
						"activites"		=>"TINYTEXT",
						"indexation"	=>"TINYTEXT"
						);

$spip_asso_profil_key = array(
						"PRIMARY KEY" => "id_profil"
						);

$tables_principales['spip_asso_profil'] = array(
		'field' => &$spip_asso_profil, 
		'key' => &$spip_asso_profil_key);

//-- Table ADHERENTS ------------------------------------------
$spip_asso_adherents = array(
						"id_adherent" 	=> "BIGINT(21) NOT NULL AUTO_INCREMENT",
						"nom" 			=> "TEXT NOT NULL ",
						"prenom" 			=> "TEXT NOT NULL ",
						"sexe" 			=> "TINYTEXT NOT NULL",
						"fonction" 		=> "TEXT",
						"email" 			=> "TINYTEXT NOT NULL",
						"validite" 			=> "DATE NOT NULL DEFAULT '0000-00-00' ",
						"numero" 			=> "TEXT NOT NULL",
						"rue" 				=> "TEXT NOT NULL ",
						"cp" 				=> "TEXT NOT NULL ",
						"ville" 				=> "TEXT NOT NULL ",
						"telephone" 		=> "TINYTEXT",
						"portable" 		=> "TINYTEXT",
						"montant" 		=> "TEXT NOT NULL",
						"date" 			=> "DATE NOT NULL DEFAULT '0000-00-00'",
						"statut" 			=> "TINYTEXT",
						"relance" 			=> "tinyint(4) NOT NULL default '0' ",						
						"divers" 			=> "TEXT",
						"remarques" 		=> "TEXT",
						"vignette" 		=> "TINYTEXT",
						"id_auteur" 		=> "int(11) default NULL",
						"id_asso" 		=> "text NOT NULL",
						"categorie" 		=> "text NOT NULL",
						"naissance" 		=> "date NOT NULL default '0000-00-00'",
						"profession" 		=> "text NOT NULL",
						"societe" 			=> "text NOT NULL",
						"identifiant" 		=> "text NOT NULL",
						"passe" 			=> "text NOT NULL",
						"creation" 		=> "date NOT NULL default '0000-00-00'",
						"maj" 				=> "timestamp(14) NOT NULL",
						"utilisateur1" 		=> "text NOT NULL",
						"utilisateur2" 		=> "text NOT NULL",
						"utilisateur3" 		=> "text NOT NULL",
						"utilisateur4" 		=> "text NOT NULL",
						"secteur"			=> "text NOT NULL",
						"publication"			=> "text NOT NULL",
						"maj" 				=> "timestamp(14) NOT NULL"
						);

$spip_asso_adherents_key = array(
						"PRIMARY KEY" => "id_adherent"
						);

$tables_principales['spip_asso_adherents'] = array(
		'field' => &$spip_asso_adherents, 
		'key' => &$spip_asso_adherents_key);

//-- Table DONS ------------------------------------------
$spip_asso_dons = array(
						"id_don" 			=> "bigint(21) NOT NULL auto_increment",
						"date_don" 		=> "date NOT NULL default '0000-00-00'",
						"bienfaiteur" 		=> "text NOT NULL",
						"id_adherent" 	=> "int(11) NOT NULL",
						"argent" 			=> "tinytext",
						"colis" 			=> "text",
						"valeur" 			=> "text NOT NULL",
						"contrepartie" 	=> "tinytext",
						"commentaire" 	=> "text",
						"maj" 				=> "timestamp(14) NOT NULL"
						);

$spip_asso_dons_key = array(
						"PRIMARY KEY" => "id_don"
						);

$tables_principales['spip_asso_dons'] = array(
		'field' => &$spip_asso_dons, 
		'key' => &$spip_asso_dons_key);	
		
//-- Table VENTES ------------------------------------------
$spip_asso_ventes = array(
						"id_vente" 		=> "BIGINT(21) AUTO_INCREMENT",
						"article"			=> "TINYTEXT NOT NULL",
						"code"			=> "TEXT NOT NULL",
						"acheteur" 		=> "TINYTEXT NOT NULL",
						"quantite" 		=> "TINYTEXT NOT NULL",
						"date_vente"		 => "DATE NOT NULL DEFAULT '0000-00-00'",
						"date_envoi" 		=> "DATE DEFAULT '0000-00-00'",
						"don" 				=> "TINYTEXT",
						"prix_vente" 		=> "TINYTEXT",
						"frais_envoi" 		=> "float NOT NULL default '0'",
						"commentaire" 	=> "TEXT",
						"maj" 				=> "timestamp(14) NOT NULL"
						);
					
$spip_asso_ventes_key = array(
						"PRIMARY KEY" => "id_vente"
						);

$tables_principales['spip_asso_ventes'] = array(
		'field' => &$spip_asso_ventes, 
		'key' => &$spip_asso_ventes_key);
	
//-- Table COMPTES ------------------------------------------
$spip_asso_comptes = array(
						"id_compte" 	=> "bigint(21) NOT NULL auto_increment",
						"date" 		=> "date default NULL",
						"recette" 		=> "float NOT NULL default '0'",
						"depense" 	=> "float NOT NULL default '0'",
						"justification" => "text",
						"imputation" 	=> "text",
						"journal" 		=> "tinytext",
						"id_journal" 	=> "int(11) NOT NULL default '0'",
						"maj" 			=> "timestamp(14) NOT NULL"
					);						
$spip_asso_comptes_key = array(
						"PRIMARY KEY" => "id_compte"
						);

$tables_principales['spip_asso_comptes'] = array(
		'field' => &$spip_asso_comptes, 
		'key' => &$spip_asso_comptes_key);

//-- Table FINANCIERS ------------------------------------------
$spip_asso_financiers = array(
						"id_financier" 	=> "int(11) NOT NULL auto_increment",
						"code" 			=> "text NOT NULL",
						"intitule" 			=> "text NOT NULL",
						"reference" 		=> "text NOT NULL",
						"solde" 			=> "float NOT NULL default '0'",
						"commentaire" 	=> "text NOT NULL",
						"maj" 				=> "timestamp(14) NOT NULL"
					);						
$spip_asso_financiers_key = array(
						"PRIMARY KEY" => "id_financier"
						);

$tables_principales['spip_asso_financiers'] = array(
		'field' => &$spip_asso_financiers, 
		'key' => &$spip_asso_financiers_key);

//-- Table LIVRES ------------------------------------------
$spip_asso_livres = array(
					"id_livre"		=> "tinyint(4) NOT NULL auto_increment",
					"valeur"		=> "text NOT NULL",
					"libelle"		=> "text NOT NULL",
					"maj"			=> " timestamp(14) NOT NULL"
					);						
$spip_asso_livres_key = array(
						"PRIMARY KEY" => "id_livre"
						);

$tables_principales['spip_asso_livres'] = array(
		'field' => &$spip_asso_livres, 
		'key' => &$spip_asso_livres_key);
		
//-- Table ACTIVITES ------------------------------------------
$spip_asso_activites = array(
					"id_activite"		=> "bigint(20) NOT NULL auto_increment",
					"id_evenement"	=> "bigint(20) NOT NULL",
					"nom"				=> "text NOT NULL",
					"id_adherent"		=> "bigint(20) NOT NULL",
					"accompagne"	=> "text NOT NULL",
					"inscrits"			=> "int(11) NOT NULL default '0'",
					"date"				=> "date NOT NULL default '0000-00-00'",
					"telephone"		=> "text NOT NULL",
					"adresse"			=> "text NOT NULL",
					"email"			=> "text NOT NULL",
					"commentaire"	=> "text NOT NULL",
					"montant"			=> "float NOT NULL default '0'",
					"date_paiement"	=> "date NOT NULL default '0000-00-00'",
					"statut"			=> "text NOT NULL",
					"maj"				=> "timestamp(14) NOT NULL"
					);						
$spip_asso_activites_key = array(
						"PRIMARY KEY" => "id_activite"
						);

$tables_principales['spip_asso_activites'] = array(
		'field' => &$spip_asso_activites, 
		'key' => &$spip_asso_activites_key);

//-- Relations ----------------------------------------------------

//global $tables_jointures;
//	$tables_jointures['spip_adherents'][]= 'bienfaiteurs';
//	$tables_jointures['spip_bienfaiteurs'][]= 'adherents';
//	$tables_jointures['spip_ventes'][]= 'banque';
//	$tables_jointures['spip_banque'][]= 'ventes';
//	$tables_jointures['spip_profil'][]= 'profil';	

//-- Table des tables ----------------------------------------------------

global $table_des_tables;
	$table_des_tables['asso_adherents'] = 'asso_adherents';
	$table_des_tables['asso_bienfaiteurs'] = 'asso_bienfaiteurs';
	$table_des_tables['asso_ventes'] = 'asso_ventes';
	$table_des_tables['asso_comptes'] = 'asso_comptes';
	$table_des_tables['asso_profil'] = 'asso_profil';
	$table_des_tables['asso_categories'] = 'asso_categories';
     $table_des_tables['asso_financiers'] = 'asso_financiers';
	$table_des_tables['asso_livres'] = 'asso_livres';
	$table_des_tables['asso_activites'] = 'asso_activites';
?>