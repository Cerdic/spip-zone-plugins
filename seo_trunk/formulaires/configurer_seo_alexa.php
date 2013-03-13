<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function formulaires_configurer_seo_alexa_charger_dist(){

	$config = unserialize($GLOBALS['meta']['seo']);
	$valeurs = isset($config['alexa']) ? $config['alexa'] : array('id' => '');
	$valeurs['alexa_id'] = $valeurs['id'];
	
	$valeurs['editable'] = true;
	
	if (!autoriser('configurer', 'configuration')) {
		$valeurs['editable'] = false;
	}
	
	return $valeurs;
}

function formulaires_configurer_seo_alexa_traiter_dist(){
	$config = unserialize($GLOBALS['meta']['seo']);
	if (!isset($config['alexa'])) {
		$config['alexa'] = array();
	}
	$config['alexa']['activate'] = _request('activate','no');
	$config['alexa']['id'] = _request('alexa_id',''); 
	$config = serialize($config);
	ecrire_meta('seo',$config);
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>
