<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function calculer_URL_SECTEUR($id_rubrique) {
	$id_secteur = sql_getfetsel(
			'id_secteur',
			'spip_rubriques',
			'id_rubrique = "'.$id_rubrique.'"',
			null,
			null,
			1);
	$url = null;

	if ($id_secteur==true)
	$url =trim(sql_getfetsel(
			'host',
			'spip_rubriques',
			'id_rubrique = "'.$id_secteur.'"',
			null,
			null,
			1
		  ));
	
	if ($url==true)
		return ((substr($url,-1)=='/')?$url:$url.'/');
		
	return strlen($GLOBAL['meta']['multidomaines_url'])>0?((substr($GLOBALS['meta']['multidomaines_url'],-1)=='/')?$GLOBALS['meta']['multidomaines_url']:$GLOBALS['meta']['multidomaines_url'].'/'):((substr($GLOBALS['meta']['adresse_site'],-1)=='/')?$GLOBALS['meta']['adresse_site']:$GLOBALS['meta']['adresse_site'].'/');
}

?>