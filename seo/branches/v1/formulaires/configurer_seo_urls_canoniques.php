<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function formulaires_configurer_seo_urls_canoniques_charger_dist(){

	$config = unserialize($GLOBALS['meta']['seo']);
	$valeurs = isset($config['canonical_url']) ? $config['canonical_url'] : array();
	
	$valeurs['editable'] = true;
	
	if (!autoriser('configurer', 'configuration')) {
		$valeurs['editable'] = false;
	}
	
	return $valeurs;
}

function formulaires_configurer_seo_urls_canoniques_traiter_dist(){
	$config = unserialize($GLOBALS['meta']['seo']);
	if (!isset($config['canonical_url'])) {
		$config['canonical_url'] = array();
	}
	$config['canonical_url']['activate'] = _request('activate','no');
	$config = serialize($config);
	ecrire_meta('seo',$config);
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>
