<?php

$GLOBALS['auteurs_complets_base_version'] = 0.07;

function auteurs_complets_verifier_base(){
	$version_base = $GLOBALS['auteurs_complets_base_version'];
	$current_version = 0.0;

	if (   (!isset($GLOBALS['meta']['auteurs_complets_base_version']) )
		|| (($current_version = $GLOBALS['meta']['auteurs_complets_base_version'])!=$version_base))

// ajout des champs additionnels a la table spip_auteurs
// si pas deja existant
	include_spip('base/abstract_sql');
	if ($current_version==0.0){	
		//definition de la table cible
		$table_nom = "spip_auteurs_ajouts";
		//création de la nouvelle table spip_auteurs_ajouts
		spip_query("CREATE TABLE ".$table_nom." (id_auteur bigint(21));");
		//ajouts des différents champs
		$desc = spip_abstract_showtable($table_nom, '', true);
		if (!isset($desc['field']['nom_famille'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `nom_famille` TEXT NOT NULL AFTER `id_auteur`");}
		if (!isset($desc['field']['prenom'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `prenom` TEXT NOT NULL AFTER `nom_famille`");}
		if (!isset($desc['field']['organisation'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `organisation` TEXT NOT NULL AFTER `prenom`");}
		if (!isset($desc['field']['url_organisation'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `url_organisation` TEXT NOT NULL AFTER `organisation`");}
		if (!isset($desc['field']['telephone'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `telephone` TEXT NOT NULL AFTER `url_organisation`");}
		if (!isset($desc['field']['fax'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `fax` TEXT NOT NULL AFTER `telephone`");}
		if (!isset($desc['field']['skype'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `skype` TEXT NOT NULL AFTER `fax`");}
		if (!isset($desc['field']['adresse'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `adresse` TEXT NOT NULL AFTER `skype`");}
		if (!isset($desc['field']['codepostal'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `codepostal` TEXT NOT NULL AFTER `adresse`");}
		if (!isset($desc['field']['ville'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `ville` TEXT NOT NULL AFTER `codepostal`");}
		if (!isset($desc['field']['pays'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `pays` TEXT NOT NULL AFTER `ville`");}
		if (!isset($desc['field']['latitude'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `latitude` TEXT NOT NULL AFTER `pays`");}
		if (!isset($desc['field']['longitude'])){
			spip_query("ALTER TABLE ".$table_nom." ADD `longitude` TEXT NOT NULL AFTER `latitude`");}
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

	if ($current_version<0.07) {
		//transfert des données depuis spip_auteurs dans spip_auteurs_ajouts
		spip_query("CREATE TABLE spip_auteurs_ajouts SELECT * FROM spip_auteurs;");
		//supprime les champs inutiles présents dans spip_auteurs
		auteurs_complets_vider_tables_old();
		//supprime les champs inutiles dans spip_auteurs_ajouts
			//récuper les noms des champs de la table spip_auteurs
		$desc = spip_abstract_showtable("spip_auteurs", '', true);
			//supprime les champs indésirables dans spip_auteurs_ajouts
		foreach($desc['field'] as $key=>$valeur) {
			if ($key != "id_auteur") {
			spip_log("ALTER TABLE spip_auteurs_ajouts DROP `".$key."`\n");
				spip_query("ALTER TABLE spip_auteurs_ajouts DROP `".$key."`");
			}
		}
		//met à jours les données meta du plugin
		ecrire_meta('auteurs_complets_base_version',$current_version=0.07);
	}

// On écris dans les champs meta le numéro de base qui nous permettra d'upgrader le plugin par la suite
	ecrire_metas();
}

	//supprime les données presente dans spip_auteurs
	function auteurs_complets_vider_tables_old() {
		include_spip('base/auteurs_complets');
		include_spip('base/abstract_sql');
		//supprime les champs inutiles dans spip_auteurs
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
	}
	
	//supprime les données depuis la table spip_auteurs_ajouts
	function auteurs_complets_vider_tables() {
		include_spip('base/auteurs_complets');
		include_spip('base/abstract_sql');
		effacer_meta('auteurs_complets_base_version');
		//supprime les anciennes données sur spip_auteurs
		auteurs_complets_vider_tables_old();
		//supprime la table spip_auteurs_ajouts
		spip_query("DROP TABLE spip_auteurs_ajouts");
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
