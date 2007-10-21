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
		'key' => &$spip_asso_categories_key
	);

	//-- Table ADHERENTS ------------------------------------------
	$spip_asso_adherents = array(
		"id_adherent"	=> "BIGINT(21) NOT NULL AUTO_INCREMENT",			//lié à id Inscription2
		"id_asso" 			=> "text NOT NULL",
		"categorie" 		=> "text NOT NULL",
		"validite" 			=> "DATE NOT NULL DEFAULT '0000-00-00' ",
		"statut_relance"	=> "text NOT NULL",
		"montant" 			=> "TEXT NOT NULL",
		"date" 				=> "DATE NOT NULL DEFAULT '0000-00-00'",
		"utilisateur1" 		=> "text NOT NULL",
		"utilisateur2" 		=> "text NOT NULL",
		"utilisateur3" 		=> "text NOT NULL",
		"utilisateur4" 		=> "text NOT NULL",
		"maj" 				=> "timestamp(14) NOT NULL"
	);
	$spip_asso_adherents_key = array(
		"PRIMARY KEY" => "id_adherent",
		"INDEX id_auteur" => "id_auteur"
	);
	$tables_principales['spip_asso_adherents'] = array(
		'field' => &$spip_asso_adherents, 
		'key' => &$spip_asso_adherents_key
	);
	
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
		'key' => &$spip_asso_dons_key
	);	
	
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
		'key' => &$spip_asso_ventes_key
	);
	
	//-- Table COMPTES ------------------------------------------
	$spip_asso_comptes = array(
		"id_compte" 	=> "bigint(21) NOT NULL auto_increment",
		"date" 			=> "date default NULL",
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
		'key' => &$spip_asso_comptes_key
	);

	//-- Table PLAN COMPTABLE ------------------------------------------
	$spip_asso_plan = array(
		"id_plan" 				=> "int(11) NOT NULL auto_increment",
		"code" 				=> "text NOT NULL",
		"intitule" 				=> "text NOT NULL",
		"classe"				=>"text NOT NULL",
		"reference" 			=> "text NOT NULL",
		"solde_anterieur"	=> "float NOT NULL default '0'",
		"date_anterieure"	=> "date NOT NULL default '0000-00-00'",
		"commentaire" 		=> "text NOT NULL",
		"maj" 					=> "timestamp(14) NOT NULL"
	);						
	$spip_asso_plan_key = array(
		"PRIMARY KEY" => "id_plan"
	);
	$tables_principales['spip_asso_plan'] = array(
		'field' => &$spip_asso_plan, 
		'key' => &$spip_asso_fplan_key
	);

	//-- Table RESSOURCES ------------------------------------------
	$spip_asso_ressources = array(
		"id_ressource"		=> "bigint(20) NOT NULL auto_increment",
		"code" 				=> "text NOT NULL",
		"intitule" 				=> "text NOT NULL",
		"date_acquisition"	=> "date NOT NULL default '0000-00-00'",
		"id_achat" 			=> "tinyint(4) NOT NULL default '0'",
		"pu" 					=> "float NOT NULL default '0'",
		"statut"				=> "text NOT NULL",
		"commentaire" 		=> "text NOT NULL",
		"maj" 					=> "timestamp(14) NOT NULL"
	);		
	$spip_asso_ressources_key = array(
		"PRIMARY KEY" => "id_ressource"
	);
	$tables_principales['spip_asso_ressources'] = array(
		'field' => &$spip_asso_ressources, 
		'key' => &$spip_asso_ressources_key
	);

	//-- Table PRETS ------------------------------------------
	$spip_asso_ressources = array(
		"id_pret"					=> "bigint(20) NOT NULL auto_increment",
		"date_sortie" 			=> "date NOT NULL default '0000-00-00'",
		"duree"					=> "int(11) NOT NULL default '0'",
		"date_retour" 			=> "date NOT NULL default '0000-00-00'",
		"id_emprunteur" 			=> "text NOT NULL",
		"statut"					=> "text NOT NULL",
		"commentaire_sortie" 	=> "text NOT NULL",
		"commentaire_retour" 	=> "text NOT NULL",
		"maj" 						=> "timestamp(14) NOT NULL"
	);		
	$spip_asso_ressources_key = array(
		"PRIMARY KEY" => "id_pret"
	);
	$tables_principales['spip_asso_prets'] = array(
		'field' => &$spip_asso_prets, 
		'key' => &$spip_asso_prets_key
	);
	
	//-- Table ACTIVITES ------------------------------------------
	$spip_asso_activites = array(
		"id_activite"		=> "bigint(20) NOT NULL auto_increment",
		"id_evenement"	=> "bigint(20) NOT NULL",
		"nom"				=> "text NOT NULL",
		"id_adherent"		=> "bigint(20) NOT NULL",
		"membres" 		=> "text NOT NULL",
		"non_membres" 	=> "text NOT NULL",
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
		'key' => &$spip_asso_activites_key
	);

	//-- Relations ----------------------------------------------------

	global $tables_jointures;

	$tables_jointures['spip_asso_adherents'][]= 'auteurs';
	$tables_jointures['spip_asso_adherents'][]= 'auteurs_elargis';
	$tables_jointures['spip_auteurs'][]= 'asso_adherents';
	$tables_jointures['spip_auteurs_elargis'][]= 'asso_adherents';

	//-- Table des tables ----------------------------------------------------

	global $table_des_tables;
	$table_des_tables['asso_adherents'] = 'asso_adherents';
	$table_des_tables['asso_dons'] = 'asso_dons';
	$table_des_tables['asso_ventes'] = 'asso_ventes';
	$table_des_tables['asso_comptes'] = 'asso_comptes';
	$table_des_tables['asso_categories'] = 'asso_categories';
     $table_des_tables['asso_plan'] = 'asso_plan';
	$table_des_tables['asso_ressources'] = 'asso_ressources';
	$table_des_tables['asso_prets'] = 'asso_prets';
	$table_des_tables['asso_activites'] = 'asso_activites';
?>