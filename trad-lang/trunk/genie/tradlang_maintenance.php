<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Génie des opérations de maintenance de tradlang
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
	$langues = sql_select('lang','spip_tradlangs','statut="OK"',"lang","COUNT(*) DESC","0,$nb_langues");
	while($langue = sql_fetch($langues)){
		$langues_utilisees[] = $langue['lang'];
	}
	ecrire_meta('tradlang_langues_max',serialize($langues_utilisees));
	return 0;
}
?>