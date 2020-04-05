<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function formulaires_configurer_seo_bing_charger_dist(){

	$config = unserialize($GLOBALS['meta']['seo']);
	$valeurs = isset($config['bing']) ? $config['bing'] : array('id' => '');
	$valeurs['bing_id'] = $valeurs['id'];
	
	$valeurs['editable'] = true;
	
	if (!autoriser('configurer', 'configuration'))
		$valeurs['editable'] = false;

	return $valeurs;
}

function formulaires_configurer_seo_bing_traiter_dist(){
	$config = unserialize($GLOBALS['meta']['seo']);
	if (!isset($config['bing']))
		$config['bing'] = array();
	$config['bing']['activate'] = _request('activate','no');
	$config['bing']['id'] = _request('bing_id',''); 
	$config = serialize($config);
	ecrire_meta('seo',$config);
	
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>
