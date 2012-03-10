<?php
/*
 * Plugin Article Accueil
 * (c) 2011 Cedric Morin, Joseph
 * Distribue sous licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Upgrade des tables
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function article_accueil_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = '0.0';
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			maj_tables('spip_rubriques');
			ecrire_meta($nom_meta_base_version,$current_version='0.1','non');
		}
	}
}


/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function article_accueil_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_alter("TABLE spip_rubriques DROP id_article_accueil");
	effacer_meta($nom_meta_base_version);
}
?>