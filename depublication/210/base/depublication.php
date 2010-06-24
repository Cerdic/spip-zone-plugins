<?php
/*
 * Plugin Depublication
 * (c) 2010 Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */

/**
 * Declaration des tables principales
 *
 * @param array $tables_principales
 * @return array
 */
function depublication_declarer_tables_principales($tables_principales){
	
	$tables_principales['spip_articles']['field']['date_depublication'] 
		= "datetime DEFAULT '00-00-00 00:00:00' NOT NULL";
	$tables_principales['spip_articles']['field']['statut_depublication'] 
		= "varchar(10) DEFAULT '0' NOT NULL";

	return $tables_principales;
}

/**
 * Upgrade des tables
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function depublication_upgrade($nom_meta_base_version, $version_cible){
	include_spip('inc/meta');
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'1.0.0','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			maj_tables('spip_articles');
			ecrire_meta($nom_meta_base_version, $current_version="1.0.0", 'non');
		}
	}
}


/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function depublication_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_alter("TABLE spip_articles DROP COLUMN date_depublication");
	sql_alter("TABLE spip_articles DROP COLUMN statut_depublication");
	effacer_meta($nom_meta_base_version);
}
?>
