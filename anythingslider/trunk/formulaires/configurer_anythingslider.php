<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_anythingslider_charger_dist(){

	$valeurs = array();
	$config =unserialize($GLOBALS['meta']['anythingslider']);
	if (!is_array($config))
		$config = array();
	$config = pipeline('anythingslider_charger',$config);
	$valeurs['_config'] = $config;
	$valeurs['_lock'] = pipeline('anythingslider_charger',array());
	return $valeurs;
}

function formulaires_configurer_anythingslider_traiter_dist(){
	$themes = is_array(_request('themes')) ? _request('themes') : array();
	$scripts = is_array(_request('scripts')) ? _request('scripts') : array();
	$config = array_merge($themes,$scripts);
	ecrire_meta('anythingslider',serialize($config));
	ecrire_metas();
	
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>
