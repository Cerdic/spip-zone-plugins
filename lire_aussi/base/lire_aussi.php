<?php
/*
 * Plugin annonce_benevolat
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


function lire_aussi_declarer_tables_principales($tables_principales){
	// ajouts dans spip_auteurs
	//id_lire bigint(21) DEFAULT '0' NOT NULL
	$articles = &$tables_principales['spip_articles'];
	$articles['field']['id_lire'] = "bigint(21) NOT NULL";
	return $tables_principales;
}


function lire_aussi_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	include_spip('base/abstract_sql');
	include_spip('base/serial');
	include_spip('base/auxiliaires');
	include_spip('base/create');
	creer_base();

	maj_tables(array(
		'spip_articles'
	));
	ecrire_meta($prefix."_base_version",$current_version=$version_cible,'non');
	return true;
}


function lire_aussi_install($action,$prefix,$version_cible){
	$version_base = $GLOBALS[$prefix."_base_version"];
	if ($GLOBALS['meta'][$prefix."_base_version"]) $version_base = $GLOBALS['meta'][$prefix."_base_version"];
	switch ($action){
		case 'test':
			$ok = (isset($GLOBALS['meta'][$prefix."_base_version"])
				AND version_compare($GLOBALS['meta'][$prefix."_base_version"],$version_cible,">="));
			if ($ok) return $ok;
			else return lire_aussi_upgrade($prefix."_base_version",$version_cible);
			break;
		case 'install':
			lire_aussi_upgrade($prefix."_base_version",$version_cible);
			break;
	}
}

?>