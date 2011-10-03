<?php

include_spip('inc/meta');

function formulaires_configurer_seo_ganalytics_charger_dist(){
	global $visiteur_session;
	$valeurs['editable'] = true;
	
	if (!autoriser('configurer', 'configuration',$visiteur_session)) {
		$valeurs['editable'] = false;
	}
	
	$config = unserialize($GLOBALS['meta']['seo']);
	$valeurs = $config['analytics'];
	$valeurs['analytics_id'] = $valeurs['id'];
	
	return $valeurs;
}

function formulaires_configurer_seo_ganalytics_traiter_dist(){
	$config = unserialize($GLOBALS['meta']['seo']);
	$config['analytics']['activate'] = _request('activate','no');
	$config['analytics']['id'] = _request('analytics_id',''); 
	$config = serialize($config);
	ecrire_meta('seo',$config);
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>