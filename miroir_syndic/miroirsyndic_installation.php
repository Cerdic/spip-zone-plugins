<?php
/*
 * Plugin miroir_syndic
 * (c) 2006-2010 Fil, Cedric
 * Distribue sous licence GPL
 *
 */

/**
 * Upgrade des tables
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function miroirsyndic_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.1.0','<')){
			include_spip('base/abstract_sql');

			sql_alter("TABLE spip_articles ADD INDEX url_site (url_site)");
			sql_alter("TABLE spip_syndic_articles ADD INDEX url (url)");
			ecrire_meta($nom_meta_base_version,$current_version="0.1.0",'non');
		}
	}
}


/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function miroirsyndic_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
?>