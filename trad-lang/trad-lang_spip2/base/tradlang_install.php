<?php
/**
 * Plugin Tradlang
 * Licence GPL (c) 2009 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function tradlang_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_tradlang','spip_tradlang_modules')),
	);
	$maj['0.3.1'] = array(
		array('sql_alter',"TABLE spip_tradlang CHANGE status status VARCHAR(16) NOT NULL DEFAULT 'OK'")
	);
	$maj['0.3.2'] = array(
		array('sql_alter',"TABLE spip_tradlang_modules CHANGE nom_mod nom_mod VARCHAR(32) NOT NULL"),
		array('sql_alter',"TABLE spip_tradlang_modules CHANGE lang_prefix lang_prefix VARCHAR(32) NOT NULL")
	);
	$maj['0.3.3'] = array(
		array('sql_alter',"TABLE spip_tradlang CHANGE status statut VARCHAR(16) NOT NULL default 'OK'"),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function tradlang_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_tradlang");
	sql_drop_table("spip_tradlang_modules");
	effacer_meta($nom_meta_base_version);
}

?>