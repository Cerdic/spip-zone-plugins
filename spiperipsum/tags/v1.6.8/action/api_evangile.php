<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function action_api_evangile_dist(){
	$res  = array();
	$arg = _request('arg');
	$arg = explode('/',$arg);
	$lang = array_shift($arg);
	$date = array_shift($arg);
	$date = strtotime($date);
	if ($lang
	  AND $date
	  AND !_IS_BOT){
		// formater correctement la date
		$date = date('Y-m-d',$date);
		include_spip("inc/filtres");
		include_spip("services/evangelizo");
		$nom_fichier = charger_lectures($lang, $date);
		lire_fichier($nom_fichier,$res);
		$res = unserialize($res);
		if (!$res)
			$res = array();
	}
	if (!function_exists('json_encode'))
		include_spip("inc/json");

	include_spip("inc/actions");
	ajax_retour(json_encode($res),"text/json");
}