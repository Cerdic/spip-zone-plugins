<?php

/**
 * Upgrade des tables
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */

function stats_data_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = 0.0;
	
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if (version_compare($current_version,'0.1','<')){
				include_spip('base/abstract_sql');
				sql_alter("TABLE spip_referers_articles ADD visites_jour int(10) unsigned not null default '0'");
				sql_alter("TABLE spip_referers_articles ADD visites_veille int(10) unsigned not null default '0'");
				ecrire_meta($nom_meta_base_version,$current_version="0.1");
		}
	}
}

/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */

function date_inscription_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_alter("TABLE spip_referers_articles DROP visites_jour");
	sql_alter("TABLE spip_referers_articles DROP visites_veille");
	effacer_meta($nom_meta_base_version);
}
