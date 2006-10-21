<?php

include_spip('inc/texte');
include_spip('inc/presentation');
include_spip('inc/acces');

function auteurs_complets_install(){
	auteurs_complets_verifier_base();
}

function auteurs_complets_uninstall(){
}

function auteurs_complets_verifier_base(){
	$version_base = 0.06;
	$current_version = 0.0;

	if (   (!isset($GLOBALS['meta']['auteurs_complets_base_version']) )
		&& (($current_version = $GLOBALS['meta']['auteurs_complets_base_version'])==$version_base))
	return;
	
	// ajout des champs additionnels a la table spip_auteurs
	// si pas deja existant

	if ($current_version==0.0){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		include_spip('base/auteurs_complets');
		creer_base();
		ecrire_meta('auteurs_complets_base_version',$current_version=$version_base);
	}
// 	if ($current_version<0.03){
// 		$desc = spip_abstract_showtable("spip_auteurs", '', true);
// 		if (!isset($desc['field']['pays'])){
// 			spip_query("ALTER TABLE spip_auteurs ADD `pays` TEXT NOT NULL AFTER `ville`");}
// 		if (!isset($desc['field']['skype'])){
// 			spip_query("ALTER TABLE spip_auteurs ADD `skype` TEXT NOT NULL AFTER `fax`");}
// 		ecrire_meta('auteurs_complets_base_version',$current_version=0.03);
// 	}
// 	if ($current_version<0.04){
// 		$desc = spip_abstract_showtable("spip_auteurs", '', true);
// 		if (!isset($desc['field']['organisation'])){
// 			spip_query("ALTER TABLE spip_auteurs ADD `organisation` TEXT NOT NULL AFTER `email`");}
// 		ecrire_meta('auteurs_complets_base_version',$current_version=0.04);
// 	}
// 	if ($current_version<0.06){
// 		spip_query("CREATE TABLE `spip_auteurs_supp` (`id_auteur` bigint( 21 ) NOT NULL AUTO_INCREMENT `nom` text NOT NULL `telephone` text NOT NULL `fax` text NOT NULL `skype` text NOT NULL `adresse` text NOT NULL `codepostal` text NOT NULL `ville` text NOT NULL `pays` text NOT NULL `latitude` text NOT NULL `longitude` idx` enum( '', '1', 'non', 'oui', 'idx' ) NOT NULL default '', PRIMARY KEY ( `id_auteur` ) , KEY `idx` ( `idx` ))");
// 		spip_query("INSERT INTO `spip_auteurs_supp` SELECT (`id_auteur`, `nom`, `idx`, `organisation` `telephone` `fax` `adresse` `codepostal` `ville` `pays` `skype` `latitude` `longitude`) FROM `spip_auteurs`");
// 		spip_query("ALTER TABLE spip_auteurs DROP (`organisation` `telephone` `fax` `adresse` `codepostal` `ville` `pays` `skype` `latitude` `longitude`)");
// 		ecrire_meta('auteurs_complets_base_version',$current_version=0.06);
// 	}
	ecrire_metas();
}

function auteurs_complets_ajouts()
{
	auteurs_complets_install();

	global $id_auteur, $redirect, $echec, $initial,
	  $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;
	
	$id_auteur = intval($id_auteur);

	$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=$id_auteur"));
	
	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');
	$legender_auteur_supp = $legender_auteur_supp($id_auteur, $auteur, $initial, $echec, $redirect);

// 	if (_request('var_ajaxcharset')) ajax_retour($legender_auteur_supp);

	return $legender_auteur_supp;
}

function exec_auteurs_complets_gestion_dist()
{
	global $id_auteur, $redirect, $echec, $initial,
	  $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;
	
	$id_auteur = intval($id_auteur);

	$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=$id_auteur"));
	
	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');
	$legender_auteur_supp = $legender_auteur_supp($id_auteur, $auteur, $initial, $echec, $redirect);

// 	if (_request('var_ajaxcharset')) ajax_retour($legender_auteur_supp);

	return $legender_auteur_supp;
}
?>