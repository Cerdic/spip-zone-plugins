<?php

function formulaires_configurer_oembed_charger_dist(){
	$valeurs = array(
		'scheme' => '',
		'endpoint' => ''
		);
	return $valeurs;
}

function formulaires_configurer_oembed_verifier_dist(){
	$erreurs = array();
	
	if (!_request('scheme'))
		$erreurs['scheme'] = _T('info_obligatoire');
	if (!_request('endpoint'))
		$erreurs['endpoint'] = _T('info_obligatoire');
	
	// verfier que ce scheme est pas déjà en base
	if (sql_getfetsel('scheme','spip_oembed_providers','scheme='.sql_quote(_request('scheme'))))
		$erreurs['scheme'] = _T('oembed:erreur_scheme_doublon');
	
	return $erreurs;
}

function formulaires_configurer_oembed_traiter_dist(){
	$messages = array();
	
	if (sql_insertq('spip_oembed_providers',array('scheme'=>_request('scheme'), 'endpoint'=>_request('endpoint'))))
		$messages['message_ok'] = _T('oembed:ok_ajout_provider');
	else
		$messages['message_erreur'] = _T('oembed:erreur_ajout_provider');
	
	return $messages;
}

?>