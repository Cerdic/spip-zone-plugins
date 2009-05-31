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

//version actuelle du plugin à changer en cas de maj

function abonnement_upgrade($nom_meta_base_version,$version_cible){

	$current_version = 0.0;
	if (   (isset($GLOBALS['meta']['abonnement_base_version']) )
			&& (($current_version = $GLOBALS['meta'][$nom_meta_base_version])==$version_cible))
		return;

	include_spip('base/create');
	include_spip('base/abstract_sql');

	if ($current_version==0.0){
		creer_base();
		//echo "creation des tables spip_abonnements";
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
	
	if ($current_version < 0.2){
		creer_base();
		echo "Maj 0.2 des tables spip_abonnements";	
		ecrire_meta($nom_meta_base_version,$current_version=0.2);
	}
	
	if ($current_version < 0.3){
		creer_base();
		echo "Maj 0.3 des tables spip_auteurs_elargis_articles";
		ecrire_meta($nom_meta_base_version,$current_version=0.3);
	}
	
	if ($current_version < 0.4){
		// faudrait virer le autoincrement aussi
		sql_alter("TABLE spip_auteurs_elargis_articles ADD INDEX id_auteur_elargi (id_auteur_elargi)");
		sql_alter("TABLE spip_auteurs_elargis_articles DROP PRIMARY KEY");
		echo "Maj 0.4 des index `spip_auteurs_elargis_articles`";
		ecrire_meta($nom_meta_base_version,$current_version=0.4);
	}
	
	if ($current_version < 0.5){
		sql_alter("TABLE spip_abonnements ADD periode text NOT NULL default '';");
		echo "Maj 0.5 de `spip_abonnements` (periode)";
		ecrire_meta($nom_meta_base_version,$current_version=0.5);
	}
	
	if ($current_version < 0.6){
		sql_alter("TABLE spip_auteurs_elargis_abonnements ADD validite datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
		sql_alter("TABLE spip_auteurs_elargis_abonnements ADD montant int(10) unsigned NOT NULL");
		echo "Maj 0.6 de `spip_auteurs_elargis_abonnements` (validite, montant)";
		ecrire_meta($nom_meta_base_version,$current_version=0.6);
	}
	
	if ($current_version < 0.61){
		sql_alter("TABLE spip_auteurs_elargis_abonnements ADD stade_relance int(10) unsigned NOT NULL");
		echo "Maj 0.61 de `spip_auteurs_elargis_abonnements` (stade_relance)";
		ecrire_meta($nom_meta_base_version,$current_version=0.61);
	}
	
	if ($current_version < 0.62){
		sql_alter("TABLE spip_auteurs_elargis_articles ADD montant int(10) unsigned NOT NULL");
		echo "Maj 0.62 de `spip_auteurs_elargis_articles` (montant)";
		ecrire_meta($nom_meta_base_version,$current_version=0.62);
	}
	
	if ($current_version < 0.70){
		sql_alter("TABLE spip_auteurs_elargis_abonnements CHANGE `date` `date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
		sql_alter("TABLE spip_auteurs_elargis_abonnements CHANGE `id_auteur_elargi` `id_auteur` INT( 10 ) UNSIGNED NOT NULL"); 
		echo "Maj 0.7 de `spip_auteurs_elargis_abonnements` (date)";
		ecrire_meta($nom_meta_base_version,$current_version=0.70);
	}
	
	if ($current_version < 0.75){
		maj_tables(array('spip_auteurs_elargis_abonnements'));
		echo "Maj 0.75 de `spip_auteurs_elargis_abonnements` (statut_paiement, hash)";
		ecrire_meta($nom_meta_base_version,$current_version=0.75);
	}


}

function abonnement_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_abonnements");
	sql_drop_table("spip_auteurs_elargis_abonnements");
	sql_drop_table("spip_auteurs_elargis_articles");
	effacer_meta($nom_meta_base_version);
}
	

		

?>
