<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_aeres_charger_dist(){
	if (isset($GLOBALS['meta']['aeres']))
		$valeurs = unserialize($GLOBALS['meta']['aeres']);
	else
		$valeurs = array(
			'debut' => '',
			'fin' => '',
			'csl' => ''
		);
	return $valeurs;
}

function formulaires_configurer_aeres_verifier_dist(){
	$erreurs = array();
	if (!_request('debut') || !intval(_request('debut'))) $erreurs['debut'] = 'Vous devez spécifier un nombre entier.';
	if (!_request('fin') || !intval(_request('fin'))) $erreurs['fin'] = 'Vous devez spécifier un nombre entier.';
	return $erreurs;
}



function formulaires_configurer_aeres_traiter_dist(){
	$config = array(
		'debut' => _request('debut'),
		'fin' => _request('fin'),
		'csl' => _request('csl'),
		'contact' => _request('contact'),
		'conference_actes' => _request('conference_actes'),
		'format_docs' => _request('format_docs')
	);
	include_spip('inc/meta');
	ecrire_meta('aeres',serialize($config));
	
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>