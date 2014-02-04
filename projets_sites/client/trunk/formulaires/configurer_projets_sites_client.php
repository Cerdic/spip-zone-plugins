<?php

function formulaires_configurer_projets_sites_client_charger_dist() {
	include_spip('inc/config');
	$valeurs = lire_config('sites_client');
	
	return $valeurs;
}

function formulaires_configurer_projets_sites_client_verifier_dist() {
	$erreurs = array();
	foreach(array('cle','actif') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = _T('info_obligatoire');

	return $erreurs;
}

function formulaires_configurer_projets_sites_client_traiter_dist() {
	$res = array();
	include_spip('inc/meta');

	$res['cle'] 		= _request('cle');
	$res['actif'] 	= _request('actif');

	if (ecrire_meta('sites_client', @serialize($res),'non')) {
		$res['message_erreur'] 		= _T('projets_sites_client:enregistrement_ko');
	}else {
		$res['message_ok'] 	= _T('projets_sites_client:enregistrement_ok');
	}

	return $res;
}

?>