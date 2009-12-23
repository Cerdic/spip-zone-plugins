<?php
/**
 * Declaration des tables principales
 *
 * @param array $tables_principales
 * @return array
 */
function date_inscription_declarer_tables_principales($tables_principales){
	
	$tables_principales['spip_auteurs']['field']['date_inscription'] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
	return $tables_principales;
}

/**
 * Upgrade des tables
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function date_inscription_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/abstract_sql');
			sql_alter("TABLE spip_auteurs ADD date_inscription datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version="0.1",'non');
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
	sql_alter("TABLE spip_auteurs DROP date_inscription");
	effacer_meta($nom_meta_base_version);
}
?>