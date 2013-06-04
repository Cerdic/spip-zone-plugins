<?php
/*
 * Plugin annonce_benevolat
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function debut_fin_declarer_tables_principales($tables_principales){

	// ajouts dans spip_auteurs
	$articles = &$tables_principales['spip_articles'];
	$articles['field']['agenda'] = "tinyint(1) NOT NULL DEFAULT '0'";
	$articles['field']['date_debut'] = "date NOT NULL";
	$articles['field']['date_fin'] = "date NOT NULL";

	return $tables_principales;
}


function debut_fin_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/abstract_sql');
		if (version_compare($current_version,"0.1.0",'<')){
			include_spip('base/serial');
			include_spip('base/auxiliaires');
			include_spip('base/create');
			creer_base();

			maj_tables(array(
				'spip_articles'
			));

			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}


function debut_fin_install($action,$prefix,$version_cible){
	$version_base = $GLOBALS[$prefix."_base_version"];
	switch ($action){
		case 'test':
			$ok = (isset($GLOBALS['meta'][$prefix."_base_version"])
				AND version_compare($GLOBALS['meta'][$prefix."_base_version"],$version_cible,">="));
			return $ok;
			break;
		case 'install':
			debut_fin_upgrade($prefix."_base_version",$version_cible);
			break;
	}
}

?>