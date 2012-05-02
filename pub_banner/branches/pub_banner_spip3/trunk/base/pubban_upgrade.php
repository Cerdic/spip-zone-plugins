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

global $pubban_tables_set;
$pubban_tables_set = array(
	'spip_pubban_publicites'=>'spip_publicites',
	'spip_pubban_emplacements'=>'spip_bannieres',
	'spip_pubban_pub_empl'=>'spip_bannieres_publicites',
	'spip_pubban_stats'=>'spip_pubban_stats',
);

function pubban_upgrade($nom_meta_base_version,$version_cible) {
	include_spip('inc/meta');
	include_spip('base/create');
	global $pubban_tables_set;
	$current_version = '0.0'; // jamais installe
	$version_new = '0.8'; // vieilles tables si < 0.8

	// Si pas installe : on creer les tables et on insere les bannieres de test
	if (!isset($GLOBALS['meta'][$nom_meta_base_version])){
		creer_base();
		foreach($GLOBALS['bannieres_site'] as $key => $value)
			sql_insertq('spip_bannieres', $value, '');
		foreach($GLOBALS['publicites_site'] as $key => $value){
			$id_empl = $value['id_banniere'];
			unset($value['id_banniere']);
			$id_pub = sql_insertq('spip_publicites', $value, '');
			if($id_pub) sql_insertq('spip_bannieres_publicites', array('id_publicite'=>$id_pub, 'id_banniere'=>$id_empl), '');
		}
		ecrire_meta($nom_meta_base_version,$version_cible,'non');
		spip_log("Plugin PUB BANNER - installation OK - tables creees en base et valeurs de tests inserees");
	}

	// Si deja installe : on met a jour et on n'insere pas les bannieres de test
	elseif (version_compare(
		$current_version = $GLOBALS['meta'][$nom_meta_base_version],
		$version_cible,"<")
	){
	
		// Si version < 0.8, on renomme les tables ...
		if (version_compare($current_version, $version_new, "<")) {
			// ancienne=>nouvelle
			foreach($pubban_tables_set as $old=>$new) {
				if ($old!=$new)
					$ok = sql_query("RENAME TABLE $old TO $new");
			}
			if ($ok)
				spip_log("Plugin PUB BANNER - renommage des tables (version '$current_version' vers '$version_cible') OK");
		}

		// Puis on met a jour les champs
		maj_tables(array(
			'spip_publicites',
			'spip_bannieres',
			'spip_pubban_stats',
			'spip_bannieres_publicites',
		));
		ecrire_meta($nom_meta_base_version,$version_cible,'non');
		spip_log("Plugin PUB BANNER - installation OK - tables mises a jour en base");
	}

	// Sinon rien a faire
	else {
		spip_log("Plugin PUB BANNER - installation OK - rien a faire version_base idem");
	}
	
	ecrire_metas();
	return true;
}

function pubban_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	global $pubban_tables_set;
	$force = (defined('PUBBAN_FORCE_UNINSTALL') AND PUBBAN_FORCE_UNINSTALL==1) ? true : false;

	// On verifie qu'il n'y ait pas de valeurs enregistrees
	$count_join = $force ? 0 : sql_countsel('spip_bannieres_publicites');
	$count_stats = $force ? 0 : sql_countsel('spip_pubban_stats');
//	echo 'join : '.$count_join.' et stats : '.$count_stats; exit;

	// Si ok, on efface
	if($count_join==0 AND $count_stats==0){
		foreach($pubban_tables_set as $old=>$new) {
			$ok = sql_drop_table($new, true);
		}
		effacer_meta('pubban_config');
		effacer_meta($nom_meta_base_version);
		spip_log("Plugin PUB BANNER - uninstall OK - sql_drop_table() et metas effaces");
		ecrire_metas();
	}
	// Sinon, on informe
	else {
		spip_log("Plugin PUB BANNER - uninstall pas possible car $count_join pubs et $count_stats statisqtiques en base ! - forcer l'effacement avec PUBBAN_FORCE_UNINSTALL=true dans 'pubban_options.php'");
		ecrire_metas();
		return false;
	}

	return true;
}

?>