<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_proprietaire_infos_cnil_charger_dist() {
	$valeurs = array(
		'date_cnil' => '',
		'numero_cnil' => '',
	);
	$datas = spip_proprio_recuperer_config();
	if ($datas and count($datas)) {
		$valeurs = array_merge($valeurs, $datas);
	}

	return $valeurs;
}

function formulaires_proprietaire_infos_cnil_verifier_dist() {
	$erreurs = array();

	return $erreurs;
}

function formulaires_proprietaire_infos_cnil_traiter_dist() {
	$datas = array(
		'date_cnil' => _request('date_cnil'),
		'numero_cnil' => _request('numero_cnil'),
	);
	if ($ok = spip_proprio_enregistrer_config($datas)) {
		return array('message_ok' => _T('spipproprio:ok_config'));
	}

	return array('message_erreur' => _T('spipproprio:erreur_config'));
}
