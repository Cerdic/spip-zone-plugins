<?php

$GLOBALS['auteurs_complets_base_version'] = 0.06;

function auteurs_complets_verifier_base(){
	$version_base = $GLOBALS['auteurs_complets_base_version'];
	$current_version = 0.0;

	if (   (!isset($GLOBALS['meta']['auteurs_complets_base_version']) )
		|| (($current_version = $GLOBALS['meta']['auteurs_complets_base_version'])!=$version_base))

// ajout des champs additionnels a la table spip_auteurs
// si pas deja existant
	include_spip('base/abstract_sql');
	if ($current_version==0.0){
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
		if (!isset($desc['field']['nom_famille'])){
			spip_query("ALTER TABLE spip_auteurs ADD `nom_famille` TEXT NOT NULL AFTER `email`");}
		if (!isset($desc['field']['prenom'])){
			spip_query("ALTER TABLE spip_auteurs ADD `prenom` TEXT NOT NULL AFTER `nom_famille`");}
		if (!isset($desc['field']['organisation'])){
			spip_query("ALTER TABLE spip_auteurs ADD `organisation` TEXT NOT NULL AFTER `prenom`");}
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
	if ($current_version<0.06){
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
		if (!isset($desc['field']['nom_famille'])){
			spip_query("ALTER TABLE spip_auteurs ADD `nom_famille` TEXT NOT NULL AFTER `email`");}
		if (!isset($desc['field']['prenom'])){
			spip_query("ALTER TABLE spip_auteurs ADD `prenom` TEXT NOT NULL AFTER `nom_famille`");}
		ecrire_meta('auteurs_complets_base_version',$current_version=0.06);
	}

// On écris dans les champs meta le numéro de base qui nous permettra d'upgrader le plugin par la suite
	ecrire_metas();
}
	
	function auteurs_complets_vider_tables() {
		include_spip('base/agenda_evenements');
		include_spip('base/abstract_sql');
		spip_query("ALTER TABLE spip_auteurs DROP `nom_famille`");
		spip_query("ALTER TABLE spip_auteurs DROP `prenom`");
		spip_query("ALTER TABLE spip_auteurs DROP `organisation`");
		spip_query("ALTER TABLE spip_auteurs DROP `url_organisation`");
		spip_query("ALTER TABLE spip_auteurs DROP `telephone`");
		spip_query("ALTER TABLE spip_auteurs DROP `fax`");
		spip_query("ALTER TABLE spip_auteurs DROP `skype`");
		spip_query("ALTER TABLE spip_auteurs DROP `adresse`");
		spip_query("ALTER TABLE spip_auteurs DROP `codepostal`");
		spip_query("ALTER TABLE spip_auteurs DROP `ville`");
		spip_query("ALTER TABLE spip_auteurs DROP `pays`");
		spip_query("ALTER TABLE spip_auteurs DROP `latitude`");
		spip_query("ALTER TABLE spip_auteurs DROP `longitude`");
		effacer_meta('auteurs_complets_base_version');
		ecrire_metas();
	}
	
	function auteurs_complets_install($action){
		$version_base = $GLOBALS['auteurs_complets_base_version'];
		switch ($action){
			case 'test':
 				return (isset($GLOBALS['meta']['auteurs_complets_base_version']) AND ($GLOBALS['meta']['auteurs_complets_base_version']>=$version_base));
				break;
			case 'install':
				auteurs_complets_verifier_base();
				break;
			case 'uninstall':
				auteurs_complets_vider_tables();
				break;
		}
	}
?>