<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function patch_05to06(){
	$sql_ajout_id_secteur = "ALTER TABLE spip_echoppe_categories ADD id_secteur BIGINT NOT NULL AFTER id_parent ;";
	$res_ajout_id_secteur = spip_query($sql_ajout_id_secteur);
	ecrire_meta('echoppe_version',$version_echoppe_locale);
	ecrire_metas();
}

function patch_06to07(){
	$sql_ajout_id_secteur = "ALTER TABLE spip_echoppe_paniers ADD statut VARCHAR(10) NOT NULL AFTER token_panier ;";
	$res_ajout_id_secteur = spip_query($sql_ajout_id_secteur);
	$sql_ajout_id_secteur2 = "ALTER TABLE spip_echoppe_paniers ADD date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER statut ;";
	$res_ajout_id_secteur2 = spip_query($sql_ajout_id_secteur2);
	$sql_creer_statuts_paniers ="CREATE TABLE `test`.`spip_echoppe_statuts_paniers` (
								`id_status_panier` BIGINT NOT NULL AUTO_INCREMENT ,
								`token_panier` VARCHAR( 255 ) NOT NULL ,
								`statut` VARCHAR( 10 ) NOT NULL ,
								`commentaires` TINYTEXT NOT NULL ,
								`date` DATETIME NOT NULL ,
								PRIMARY KEY ( `id_status_panier` )
								)";
	$res_creer_statuts_paniers = spip_query($sql_creer_statuts_paniers);	
	ecrire_meta('echoppe_version',$version_echoppe_locale);
	ecrire_metas();
}

?>
