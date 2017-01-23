<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/texte');

function formulaires_proprietaire_textes_charger_dist($raccourci = '', $config_fonc = 'spip_proprio_form_config') {
	$valeurs = array();
	spip_proprio_proprietaire_texte();
	$tableau = textes_proprietaire(true);

	foreach ($tableau as $nom => $val) {
		if ($nom == $raccourci) {
			$valeurs = array('raccourci' => $raccourci, 'value' => $val);
		}
	}

	if ($config_fonc) {
		$valeurs['config'] = $config = $config_fonc();
	}

	return $valeurs;
}

function formulaires_proprietaire_textes_verifier_dist() {
	$erreurs = array();

	if (!$raccourci = _request('raccourci')) {
		$erreurs['raccourci'] = _T('info_obligatoire');
	}
	if (!$texte = _request('value')) {
		$erreurs['value'] = _T('info_obligatoire');
	}

	return $erreurs;
}

function formulaires_proprietaire_textes_traiter_dist($raccourci = '') {
	$message = array();
	include_spip('spip_proprio_fonctions');
	$raccourci_nouveau = spip_proprio_formater_nom_fichier(_request('raccourci'));

	if ($a = traiter_textes_proprietaire($raccourci_nouveau)) {
		include_spip('inc/headers');
		if ($redirect = redirige_formulaire(generer_url_ecrire('spip_proprio_textes'))) {
			return $redirect;
		}

		return array('message_ok' => _T('spipproprio:ok_config'));
	}

	return array('message_erreur' => _T('spipproprio:erreur_config'));
}
