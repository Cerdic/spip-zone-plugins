<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function patch_05to06(){
	spip_log("Mise à jour de echoppeBD -> 0.6");
	$sql_ajout_id_secteur = "ALTER TABLE spip_echoppe_categories ADD id_secteur BIGINT NOT NULL AFTER id_parent ;";
	$res_ajout_id_secteur = spip_query($sql_ajout_id_secteur);
	ecrire_meta('echoppedb_version',"0.6");
	ecrire_metas();
}

function patch_06to07(){
	spip_log("Mise à jour de echoppeBD -> 0.7");
	$sql_ajout_id_secteur = "ALTER TABLE spip_echoppe_paniers ADD statut VARCHAR(10) NOT NULL AFTER token_panier ;";
	$res_ajout_id_secteur = spip_query($sql_ajout_id_secteur);
	$sql_ajout_id_secteur2 = "ALTER TABLE spip_echoppe_paniers ADD date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER statut ;";
	$res_ajout_id_secteur2 = spip_query($sql_ajout_id_secteur2);
	$sql_creer_statuts_paniers ="CREATE TABLE `spip_echoppe_statuts_paniers` (
								`id_status_panier` BIGINT NOT NULL AUTO_INCREMENT ,
								`token_panier` VARCHAR( 255 ) NOT NULL ,
								`statut` VARCHAR( 10 ) NOT NULL ,
								`commentaires` TINYTEXT NOT NULL ,
								`date` DATETIME NOT NULL ,
								PRIMARY KEY ( `id_status_panier` )
								)";
	$res_creer_statuts_paniers = spip_query($sql_creer_statuts_paniers);	
	ecrire_meta('echoppedb_version',"0.7");
	ecrire_metas();
}

function patch_07to08(){
	spip_log("Mise à jour de echoppeBD -> 0.8");
	$sql_rename_table = "RENAME TABLE spip_echoppe_options_descriptifs TO spip_echoppe_options_descriptions;";
	$res_rename_table = spip_query($sql_rename_table);
	ecrire_meta('echoppedb_version',"0.8");
	ecrire_metas();
}

function patch_08to09(){
	spip_log("Mise à jour de echoppeBD -> 0.9");
	$sql_reparer_primary_2 = "ALTER TABLE `spip_echoppe_options` DROP INDEX `id_option` ";
	$res_reparer_primary_2 = spip_query($sql_reparer_primary_2);
	$sql_reparer_primary = "ALTER TABLE `spip_echoppe_options` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id_option` ) ";
	$res_reparer_primary = spip_query($sql_reparer_primary);
	$sql_reparer_primary_2 = "ALTER TABLE `spip_echoppe_options` CHANGE `id_option` `id_option` BIGINT( 21 ) NOT NULL AUTO_INCREMENT";
	
	ecrire_meta('echoppedb_version',"0.9");
	ecrire_metas();
}

function patch_09to10(){
	spip_log("Mise à jour de echoppeBD -> 1.0");
	
	// Il faut ecrire le code de la maj si on veut garder la compatibilité avec les versions de developpement ...
	
	ecrire_meta('echoppedb_version',"1.0");
	ecrire_metas();
}

?>
