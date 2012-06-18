<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function calculer_URL_SECTEUR($id_rubrique) {
	$id_secteur = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval($id_rubrique));

	$url = null;

	if ($id_secteur==true)
	$url =trim(sql_getfetsel("host", "spip_rubriques", "id_rubrique=" . intval($id_rubrique)));
	
	if ($url==true)
		return ((substr($url,-1)=='/')?$url:$url.'/');
		
	return strlen($GLOBAL['meta']['multidomaines/editer_url'])>0?((substr($GLOBALS['meta']['multidomaines/editer_url'],-1)=='/')?$GLOBALS['meta']['multidomaines/editer_url']:$GLOBALS['meta']['multidomaines/editer_url'].'/'):((substr($GLOBALS['meta']['multidomaines/adresse_site'],-1)=='/')?$GLOBALS['meta']['multidomaines/adresse_site']:$GLOBALS['meta']['multidomaines/adresse_site'].'/');
}

?>
