<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_proprietaire_adresse_charger_dist($who = 'proprietaire') {
	$conf = spip_proprio_recuperer_config();
	$valeurs = array(
		'who' => $who,
		'adresse_rue' => isset($conf[$who.'_adresse_rue']) ? $conf[$who.'_adresse_rue'] : '',
		'adresse_code_postal' => isset($conf[$who.'_adresse_code_postal']) ? $conf[$who.'_adresse_code_postal'] : '',
		'adresse_ville' => isset($conf[$who.'_adresse_ville']) ? $conf[$who.'_adresse_ville'] : '',
		'adresse_pays' => isset($conf[$who.'_adresse_pays']) ? $conf[$who.'_adresse_pays'] : 'France',
		'adresse_telephone' => isset($conf[$who.'_adresse_telephone']) ? $conf[$who.'_adresse_telephone'] : '',
		'adresse_telecopie' => isset($conf[$who.'_adresse_telecopie']) ? $conf[$who.'_adresse_telecopie'] : '',
	);

	return $valeurs;
}

function formulaires_proprietaire_adresse_verifier_dist($who = 'proprietaire') {
	$erreurs = array();

	return $erreurs;
}

function formulaires_proprietaire_adresse_traiter_dist($who = 'proprietaire') {
	$datas = array(
		$who.'_adresse_rue' => _request('adresse_rue'),
		$who.'_adresse_code_postal' => _request('adresse_code_postal'),
		$who.'_adresse_ville' => _request('adresse_ville'),
		$who.'_adresse_pays' => _request('adresse_pays'),
		$who.'_adresse_telephone' => _request('adresse_telephone'),
		$who.'_adresse_telecopie' => _request('adresse_telecopie'),
	);
	
	if ($ok = spip_proprio_enregistrer_config($datas)) {
		return array('message_ok' => _T('spipproprio:ok_config'));
	}

	return array('message_erreur' => _T('spipproprio:erreur_config'));
}
