<?php
/**
* Plugin abonnement
*
* Copyright (c) 2011
* Anne-lise Martenot elastick.net / BoOz booz@rezo.net 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt
*  
**/

include_spip('inc/meta');
include_spip('base/create');

function abonnement_upgrade($nom_meta_base_version,$version_cible){
	
	$current_version = 0.0; //suppose que le plugin n'a jamais ete installe
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];


	if (version_compare($current_version,"0.75","<=")){
		if (_DEBUG_ABONNEMENT) spip_log('Renommage des champs de abonnements et bascule des champs auteurs_elargis','abonnement');
		abonnement_modifier_tables($nom_meta_base_version);
		creer_base();
			recuperer_auteurs_elargis_abonnements();
			recuperer_auteurs_elargis_articles();
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
	
	if (version_compare($current_version,"1.3","<=")){
		sql_alter("TABLE spip_abonnements ADD nb_rub bigint(21) not null default 0 AFTER exact");
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

//bascule de spip_auteurs_elargis_abonnements
function recuperer_auteurs_elargis_abonnements(){
	$lignes = sql_allfetsel(
				'*',
				'spip_auteurs_elargis_abonnements'
			);
	include_spip('action/editer_contacts_abonnement');
	foreach($lignes as $abo){
		if($abo['statut_paiement']=='ok')
			$statut_abonnement="paye";
		else $statut_abonnement=$abo['statut_paiement'];
		
		$arg['objet']='abonnement';			
		$arg['id_auteur']=$abo['id_auteur'];
		$arg['id_objet']=$abo['id_abonnement'];
		$arg['prix']=$abo['montant'];
		$arg['date']=$abo['date'];
		$arg['validite']=$abo['validite'];
		$arg['statut_abonnement']=$statut_abonnement;
		$arg['stade_relance']=$abo['stade_relance'];
		//on bascule les champs sur la nouvelle table
		insert_contacts_abonnement($arg);	
	}
}

//bascule de spip_auteurs_elargis_articles
function recuperer_auteurs_elargis_articles(){
	$lignes = sql_allfetsel(
				'*',
				'spip_auteurs_elargis_articles'
			);
	include_spip('action/editer_contacts_abonnement');
	foreach($lignes as $abo){
		if($abo['statut_paiement']=='ok')
			$statut_abonnement="paye";
		else $statut_abonnement=$abo['statut_paiement'];
		
		$arg['objet']='article';			
		$arg['id_auteur']=$abo['id_auteur_elargi'];
		$arg['id_objet']=$abo['id_article'];
		$arg['date']=$abo['date'];
		$arg['prix']=$abo['montant'];
		$arg['statut_abonnement']=$abo['statut_paiement'];
		//on bascule les champs sur la nouvelle table
		insert_contacts_abonnement($arg);	
	}
}

function abonnement_modifier_tables($nom_meta_base_version) {
sql_alter("TABLE spip_abonnements CHANGE `libelle` titre text DEFAULT '' NOT NULL");
sql_alter("TABLE spip_abonnements ADD ids_zone text NOT NULL AFTER periode");
sql_alter("TABLE spip_abonnements CHANGE `montant` prix float not null default 0");
sql_alter("TABLE spip_abonnements CHANGE `commentaire` descriptif text DEFAULT '' NOT NULL");
}	

		

?>
