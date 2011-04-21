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
	$maj['0.3.4'] = array(
		array('sql_alter',"TABLE spip_tradlang ADD id_tradlang_module bigint(21) DEFAULT '0' NOT NULL"),
		array('tradlang_maj_id_tradlang_modules',true)
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function tradlang_maj_id_tradlang_modules($affiche = false){
	$strings = array_map('reset',sql_allfetsel('id_tradlang','spip_tradlang',"id_tradlang_module='0'",'','',"0,100"));
	while (count($strings)){
		foreach($strings as $id_tradlang){
			$module = sql_getfetsel('module','spip_tradlang','id_tradlang='.intval($id_tradlang));
			$id_tradlang_module = sql_getfetsel('id_tradlang_module','spip_tradlang_modules','module='.sql_quote($module));
			sql_updateq('spip_tradlang',array('id_tradlang_module' => $id_tradlang_module),'id_tradlang='.intval($id_tradlang));
		}
		if ($affiche) echo " .";
	  	$strings = array_map('reset',sql_allfetsel('id_tradlang','spip_tradlang',"id_tradlang_module='0'",'','',"0,100"));
	}
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