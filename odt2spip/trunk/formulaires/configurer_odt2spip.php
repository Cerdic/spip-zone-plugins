<?php

function formulaires_configurer_odt2spip_charger_dist() {
	include_spip('inc/config');
	include_spip('inc/odt2spip');
	$valeurs = lire_config('odt2spip', array());
	$valeurs += array(
		'defaut_attacher' => '',
		'authorized_keys' => '',
		'serveur_api_url' => '',
		'serveur_api_cle' => '',
		'_libreoffice_ok' => odt2spip_commande_libreoffice_disponible(),
	);

	return $valeurs;
}


function formulaires_configurer_odt2spip_verifier_dist() {
	$erreurs = array();
	return $erreurs;
}


function formulaires_configurer_odt2spip_traiter_dist() {
	include_spip('inc/modifier');
	$set = collecter_requests(array('defaut_attacher', 'authorized_keys', 'serveur_api_url', 'serveur_api_cle'));

	include_spip('inc/config');
	include_spip('inc/odt2spip');
	$valeurs = lire_config('odt2spip', array());
	$set += $valeurs;

	if (_request('generer_cle')) {
		$cle = md5(uniqid(rand(), true));
		$set['authorized_keys'] = trim($set['authorized_keys'] . "\n" . $cle . ' : Nouveau site' );
		set_request('authorized_keys', $set['authorized_keys']);
	}

	ecrire_config('odt2spip', $set);

	$res = array(
		'editable' => true,
		'message_ok' => _T('config_info_enregistree')
	);

	if (_request('generer_cle')) {
		$res['message_ok'] .= '<br />' . _T('odtspip:nouvelle_cle_api_generee');
	}

	return $res;
}