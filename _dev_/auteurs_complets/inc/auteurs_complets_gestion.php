<?php

include_spip('inc/presentation');

// La fonction qui en appelle une autre et qui va nous modifier notre base de donnee comme on en a envie...
function auteurs_complets_install(){
	auteurs_complets_verifier_base();
}

// La fonction qui désinstallera le plugin proprement... n'existe pas encore...
function auteurs_complets_uninstall(){
}

// La fonction qui modifie la base de donnée
function auteurs_complets_verifier_base(){
	$version_base = 0.05;
	$current_version = 0.0;

	if (   (!isset($GLOBALS['meta']['auteurs_complets_base_version']) )
		&& (($current_version = $GLOBALS['meta']['auteurs_complets_base_version'])==$version_base))
	return;

// ajout des champs additionnels a la table spip_auteurs

// si pas deja existant

	if ($current_version==0.0){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
		if (!isset($desc['field']['organisation'])){
			spip_query("ALTER TABLE spip_auteurs ADD `organisation` TEXT NOT NULL AFTER `email`");}
		if (!isset($desc['field']['url_organisation'])){
			spip_query("ALTER TABLE spip_auteurs ADD `url_organisation` TEXT NOT NULL AFTER `organisation`");}
		if (!isset($desc['field']['telephone'])){
			spip_query("ALTER TABLE spip_auteurs ADD `telephone` TEXT NOT NULL AFTER `url_organisation`");}
		if (!isset($desc['field']['fax'])){
			spip_query("ALTER TABLE spip_auteurs ADD `fax` TEXT NOT NULL AFTER `telephone`");}
		if (!isset($desc['field']['skype'])){
			spip_query("ALTER TABLE spip_auteurs ADD `skype` TEXT NOT NULL AFTER `fax`");}
		if (!isset($desc['field']['adresse'])){
			spip_query("ALTER TABLE spip_auteurs ADD `adresse` TEXT NOT NULL AFTER `skype`");}
		if (!isset($desc['field']['codepostal'])){
			spip_query("ALTER TABLE spip_auteurs ADD `codepostal` TEXT NOT NULL AFTER `adresse`");}
		if (!isset($desc['field']['ville'])){
			spip_query("ALTER TABLE spip_auteurs ADD `ville` TEXT NOT NULL AFTER `codepostal`");}
		if (!isset($desc['field']['pays'])){
			spip_query("ALTER TABLE spip_auteurs ADD `pays` TEXT NOT NULL AFTER `ville`");}
		if (!isset($desc['field']['latitude'])){
			spip_query("ALTER TABLE spip_auteurs ADD `latitude` TEXT NOT NULL AFTER `pays`");}
		if (!isset($desc['field']['longitude'])){
			spip_query("ALTER TABLE spip_auteurs ADD `longitude` TEXT NOT NULL AFTER `latitude`");}
			ecrire_meta('auteurs_complets_base_version',$current_version=$version_base);
	}

// Si la base existe déjà on la modifie en fonction de la version déjà installée...
	if ($current_version<0.03){
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
		if (!isset($desc['field']['pays'])){
			spip_query("ALTER TABLE spip_auteurs ADD `pays` TEXT NOT NULL AFTER `ville`");}
		if (!isset($desc['field']['skype'])){
			spip_query("ALTER TABLE spip_auteurs ADD `skype` TEXT NOT NULL AFTER `fax`");}
		ecrire_meta('auteurs_complets_base_version',$current_version=0.03);
	}
	if ($current_version<0.04){
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
		if (!isset($desc['field']['organisation'])){
			spip_query("ALTER TABLE spip_auteurs ADD `organisation` TEXT NOT NULL AFTER `email`");}
		ecrire_meta('auteurs_complets_base_version',$current_version=0.04);
	}
	if ($current_version<0.05){
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
		if (!isset($desc['field']['url_organisation'])){
			spip_query("ALTER TABLE spip_auteurs ADD `url_organisation` TEXT NOT NULL AFTER `organisation`");}
		ecrire_meta('auteurs_complets_base_version',$current_version=0.05);
	}

// On écris dans les champs meta le numéro de base qui nous permettra d'upgrader le plugin par la suite
	ecrire_metas();
}

function auteurs_complets_ajouts()
{
// A chaque appel on vérifie si la base est correctement installée...
	auteurs_complets_install();

// On récupère les globales nécessaires
	global $id_auteur, $redirect, $echec, $initial,
	  $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;
	
	$id_auteur = intval($id_auteur);

// On crée un array des données associées à un auteur...
	$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=$id_auteur"));

// On récupère le fichier qui contient ce dont on a besoin
	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');

// On lui passe en paramètre ce qui nous est nécessaire
	$legender_auteur_supp_total = $legender_auteur_supp($id_auteur, $auteur, $initial, $echec, $redirect);

	if (_request('var_ajaxcharset')) ajax_retour($legender_auteur_supp_total);

// On balance ce dont on a besoin
	return $legender_auteur_supp_total;
}
?>