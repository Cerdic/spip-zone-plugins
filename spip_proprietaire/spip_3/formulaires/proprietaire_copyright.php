<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_proprietaire_copyright_charger_dist() {
	$valeurs = array(
		'copyright_annee' => '',
		'copyright_complement' => '',
		'copyright_comment' => _T('spipproprio:copyright_default_comment_multi'),
	);

	$datas = spip_proprio_recuperer_config();
	
	if ($datas and count($datas)) {
		$valeurs = array_merge($valeurs, $datas);
	}

	return $valeurs;
}

function formulaires_proprietaire_copyright_verifier_dist() {
	$erreurs = array();

	return $erreurs;
}

function formulaires_proprietaire_copyright_traiter_dist() {
	$datas = array(
		'copyright_annee' => _request('copyright_annee'),
		'copyright_complement' => _request('copyright_complement'),
		'copyright_comment' => _request('copyright_comment'),
	);

	if ($ok = spip_proprio_enregistrer_config($datas)) {
		return array('message_ok' => _T('spipproprio:ok_config'));
	}

	return array('message_erreur' => _T('spipproprio:erreur_config'));
}
