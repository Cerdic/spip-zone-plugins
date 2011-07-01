<?php
/**
 * @name 		Installation
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/pubban_chargeur');
include_spip('base/abstract_sql');

function pubban_upgrade($nom_meta_base_version,$version_cible) {
	include_spip('inc/meta');
	$current_version = 0.0;
	if (!isset($GLOBALS['meta'][$nom_meta_base_version])
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/create');
		maj_tables(array(
			$GLOBALS['_PUBBAN_CONF']['table_pub'],
			$GLOBALS['_PUBBAN_CONF']['table_empl'],
			$GLOBALS['_PUBBAN_CONF']['table_stats'],
			$GLOBALS['_PUBBAN_CONF']['table_join'],
		), _BDD_PUBBAN);
		foreach($GLOBALS['emplacements_site'] as $key => $value)
			sql_insertq($GLOBALS['_PUBBAN_CONF']['table_empl'], $value, '', _BDD_PUBBAN);
		foreach($GLOBALS['publicites_site'] as $key => $value){
			$id_empl = $value['id_empl'];
			unset($value['id_empl']);
			$id_pub = sql_insertq($GLOBALS['_PUBBAN_CONF']['table_pub'], $value, '', _BDD_PUBBAN);
			if($id_pub) sql_insertq($GLOBALS['_PUBBAN_CONF']['table_join'], array('id_pub'=>$id_pub, 'id_empl'=>$id_empl), '', _BDD_PUBBAN);
		}
		ecrire_meta($nom_meta_base_version,$version_cible,'non');
		spip_log("Plugin PUB BANNER - install OK - tables creees en base");
	}
	else spip_log("Plugin PUB BANNER - install OK - rien a faire version_base idem");
}

function pubban_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');

	$force = (defined('PUBBAN_FORCE_UNINSTALL') AND PUBBAN_FORCE_UNINSTALL==1) ? true : false;
	// On verifie qu'il n'y ait pas de valeurs enregistrees
	$count_join = $force ? 0 : sql_countsel($GLOBALS['_PUBBAN_CONF']['table_join']);
	$count_stats = $force ? 0 : sql_countsel($GLOBALS['_PUBBAN_CONF']['table_stats']);
//	echo 'join : '.$count_join.' et stats : '.$count_stats; exit;
	// Si ok, on efface
	if($count_join==0 AND $count_stats==0){
		sql_drop_table($GLOBALS['_PUBBAN_CONF']['table_join'], "", _BDD_PUBBAN);
		sql_drop_table($GLOBALS['_PUBBAN_CONF']['table_pub'], "", _BDD_PUBBAN);
		sql_drop_table($GLOBALS['_PUBBAN_CONF']['table_empl'], "", _BDD_PUBBAN);
		sql_drop_table($GLOBALS['_PUBBAN_CONF']['table_stats'], "", _BDD_PUBBAN);
		effacer_meta('pubban_config');
		effacer_meta($nom_meta_base_version);
		spip_log("Plugin PUB BANNER - uninstall OK - champs effaces en base et metas effaces");
	}
	// Sinon, on informe
	else {
		spip_log("Plugin PUB BANNER - uninstall pas possible car $count_join pubs et $count_stats statisqtiques en base ! - forcer l'effacement avec PUBBAN_FORCE_UNINSTALL=true dans 'pubban_options.php'");
		return false;
	}
	return true;
}
?>