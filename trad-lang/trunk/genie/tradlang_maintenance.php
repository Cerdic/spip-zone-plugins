<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Génie des opérations de maintenance journalières de tradlang
 */
function genie_tradlang_maintenance_dist($t) {
	
	/**
	 * Stocker en base un tableau serializé 
	 * des langues les plus traduites en suivant la configuration de tradlang
	 */
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	$nb_langues = lire_config('tradlang/limiter_langues_bilan_nb','10');
	$langues_utilisees = array();
	$langues = sql_allfetsel('lang','spip_tradlangs','statut="OK"',"lang","COUNT(*) DESC","0,$nb_langues");
	foreach($langues as $langue){
		$langues_utilisees[] = $langue['lang'];
	}
	ecrire_meta('tradlang_langues_max',serialize($langues_utilisees));
	
	/**
	 * Suppression des versions des tradlangs disparus
	 */
	$tradlang_disparus = sql_allfetsel('versions.id_objet','spip_versions AS versions','versions.objet="tradlang" AND NOT EXISTS(SELECT * FROM spip_tradlangs AS tradlangs WHERE versions.id_objet = tradlangs.id_tradlang)','versions.id_objet');
	$disparus = array();
	foreach($tradlang_disparus as $tradlang){
		$disparus[] = $tradlang['id_objet'];
	}
	if(count($disparus) && count($disparus) > 0){
		sql_delete('spip_versions','objet="tradlang" AND '.sql_in('id_objet',$disparus));
		sql_delete('spip_versions_fragments','objet="tradlang" AND '.sql_in('id_objet',$disparus));
	}
	return 0;
}
?>