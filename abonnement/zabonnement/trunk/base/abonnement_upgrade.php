<?php
/**
* Plugin abonnement
*
* Copyright (c) 2011
* Anne-lise Martenot elastick.net / BoOz booz@rezo.net 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

include_spip('inc/meta');
include_spip('base/create');

function abonnement_upgrade($nom_meta_base_version,$version_cible){
	
	$current_version = 0.0; //suppose que le plugin n'a jamais ete installe
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];


	if (version_compare($current_version,"0.75","<=")){
		if (_DEBUG_ABONNEMENT) spip_log('il faut renommer les tables spip_abonnements','abonnement');
		abonnement_modifier_tables($nom_meta_base_version);
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
	
	if (version_compare($current_version,"1.1","<=")){
		sql_alter("TABLE spip_abonnements ADD COLUMN exact ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER periode");
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
	
	if (version_compare($current_version,"1.2","<=")){
		sql_alter("TABLE spip_contacts_abonnements ADD id_contacts_abonnement bigint(21) not null FIRST");
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
	
	//jamais installe
	if ($current_version==0.0){
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}

}

function abonnement_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_abonnements");
	sql_drop_table("spip_contacts_abonnements");
	effacer_meta($nom_meta_base_version);
}

//ancien
//id_abonnement libelle duree 	periode 	montant 	commentaire 	maj
//nouveau
// id_abonnement titre 	duree 	periode 	ids_zone 	prix 	descriptif 	maj 
// + court = sql_alter('TABLE spip_abonnements RENAME TO spip_abonnementsOLD');

function abonnement_modifier_tables($nom_meta_base_version) {
sql_alter("TABLE spip_abonnements CHANGE `libelle` titre text DEFAULT '' NOT NULL");
sql_alter("TABLE spip_abonnements ADD ids_zone text NOT NULL AFTER periode");
sql_alter("TABLE spip_abonnements CHANGE `montant` prix float not null default 0");
sql_alter("TABLE spip_abonnements CHANGE `commentaire` descriptif text DEFAULT '' NOT NULL");
}	

		

?>
