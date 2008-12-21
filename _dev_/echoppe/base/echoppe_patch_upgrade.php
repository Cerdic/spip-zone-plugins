<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function patch_05to06(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.6");
	$sql_ajout_id_secteur = "ALTER TABLE spip_echoppe_categories ADD id_secteur BIGINT NOT NULL AFTER id_parent ;";
	$res_ajout_id_secteur = spip_query($sql_ajout_id_secteur);
	ecrire_meta('echoppe_base_version',"0.6");
	ecrire_metas();
}

function patch_06to07(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.7");
	$sql_ajout_id_secteur = "ALTER TABLE spip_echoppe_paniers ADD statut VARCHAR(10) NOT NULL AFTER token_panier ;";
	$res_ajout_id_secteur = spip_query($sql_ajout_id_secteur);
	$sql_ajout_id_secteur2 = "ALTER TABLE spip_echoppe_paniers ADD date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER statut ;";
	$res_ajout_id_secteur2 = spip_query($sql_ajout_id_secteur2);
	$sql_creer_statuts_paniers ="CREATE TABLE spip_echoppe_statuts_paniers (
								id_status_panier BIGINT NOT NULL AUTO_INCREMENT ,
								token_panier VARCHAR( 255 ) NOT NULL ,
								statut VARCHAR( 10 ) NOT NULL ,
								commentaires TINYTEXT NOT NULL ,
								date DATETIME NOT NULL ,
								PRIMARY KEY ( id_status_panier )
								)";
	$res_creer_statuts_paniers = spip_query($sql_creer_statuts_paniers);	
	ecrire_meta('echoppe_base_version',"0.7");
	ecrire_metas();
}

function patch_07to08(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.8");
	$sql_rename_table = "RENAME TABLE spip_echoppe_options_descriptifs TO spip_echoppe_options_descriptions;";
	$res_rename_table = spip_query($sql_rename_table);
	ecrire_meta('echoppe_base_version',"0.8");
	ecrire_metas();
}

function patch_08to09(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.9");
	$sql_reparer_primary_2 = "ALTER TABLE spip_echoppe_options DROP INDEX id_option ";
	$res_reparer_primary_2 = spip_query($sql_reparer_primary_2);
	$sql_reparer_primary = "ALTER TABLE spip_echoppe_options DROP PRIMARY KEY , ADD PRIMARY KEY ( id_option ) ";
	$res_reparer_primary = spip_query($sql_reparer_primary);
	$sql_reparer_primary_2 = "ALTER TABLE spip_echoppe_options CHANGE id_option id_option BIGINT( 21 ) NOT NULL AUTO_INCREMENT";
	
	ecrire_meta('echoppe_base_version',"0.9");
	ecrire_metas();
}

function patch_09to10(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.3.0");
	
	// Il faut ecrire le code de la maj si on veut garder la compatibilité avec les versions de developpement ...
	
	ecrire_meta('echoppe_base_version',"0.3.0");
	ecrire_metas();
}

function patch_10to11(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.3.1");
	sql_alter('TABLE spip_echoppe_produits ADD id_trad BIGINT NOT NULL AFTER lang ;');
	ecrire_meta('echoppe_base_version',"0.3.1");
	ecrire_metas();
}
function patch_11to12(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.3.2");
	sql_alter('TABLE spip_echoppe_depots ADD numero_depot VARCHAR( 10 ) NOT NULL AFTER adresse ,
	ADD code_postal_depot TINYTEXT NOT NULL AFTER numero_depot ,
	ADD ville_depot TINYINT NOT NULL AFTER code_postal_depot ;');
	ecrire_meta('echoppe_base_version',"0.3.2");
	ecrire_metas();
}
function patch_12to13(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.3.3");
	sql_alter('TABLE spip_echoppe_depots ADD pays_depot VARCHAR( 255 ) NOT NULL AFTER code_postal_depot ,
	ADD telephone_depot VARCHAR( 15 ) NOT NULL AFTER pays_depot ,
	ADD gsm_depot VARCHAR( 15 ) NOT NULL AFTER telephone_depot ,
	ADD email_depot VARCHAR( 255 ) NOT NULL AFTER gsm_depot ,
	TABLE spip_echoppe_stocks CHANGE ref_produit ref_produit VARCHAR( 255 ) NOT NULL ;');
	sql_alter('TABLE spip_echoppe_depots ADD fax_depot VARCHAR( 255 ) NOT NULL AFTER gsm_depot;');
	sql_alter('TABLE spip_echoppe_depots CHANGE adresse adresse_depot TINYTEXT NOT NULL;');
	sql_alter('TABLE spip_echoppe_depots CHANGE descriptif description TEXT NOT NULL;');
	ecrire_meta('echoppe_base_version',"0.3.3");
	ecrire_metas();
}

function patch_13to14(){
	include_spip('base/create');
	include_spip('base/abstract_sql');
	spip_log("Mise à jour de echoppeBD -> 0.3.4");
	sql_alter("TABLE spip_echoppe_paniers ADD date_maj datetime DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER date ;");
	sql_alter("TABLE spip_echoppe_paniers CHANGE id_clients id_client BIGINT( 21 ) NOT NULL ,
CHANGE token_clients token_client VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");
	ecrire_meta('echoppe_base_version',"0.3.4");
	ecrire_metas();
}
?>
