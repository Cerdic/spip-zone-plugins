<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function formulaires_configurer_seo_webmaster_tools_charger_dist(){
	global $visiteur_session;
	
	$config = unserialize($GLOBALS['meta']['seo']);
	$valeurs = $config['webmaster_tools'];
	$valeurs['webmaster_tools_id'] = $valeurs['id'];
	
	$valeurs['editable'] = true;
	
	if (!autoriser('configurer', 'configuration',$visiteur_session)) {
		$valeurs['editable'] = false;
	}

	return $valeurs;
}

function formulaires_configurer_seo_webmaster_tools_traiter_dist(){
	$config = unserialize($GLOBALS['meta']['seo']);
	$config['webmaster_tools']['activate'] = _request('activate','no');
	$config['webmaster_tools']['id'] = _request('webmaster_tools_id',''); 
	$config = serialize($config);
	ecrire_meta('seo',$config);
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>