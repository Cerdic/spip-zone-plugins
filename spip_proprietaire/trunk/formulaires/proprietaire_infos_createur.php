<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_proprietaire_infos_createur_charger_dist() {
	$conf = spip_proprio_recuperer_config();
	$valeurs = array(
		'createur_administrateur' => (isset($conf['createur_administrateur']) and $conf['createur_administrateur'] == 'oui') ? 'oui' : 'non',
	);

	return $valeurs;
}

function formulaires_proprietaire_infos_createur_verifier_dist() {
	$erreurs = array();

	return $erreurs;
}

function formulaires_proprietaire_infos_createur_traiter_dist() {
	$datas = array(
		'createur_administrateur' => ($oui = _request('createur_administrateur') and $oui == 'oui') ? 'oui' : 'non',
	);
	if ($ok = spip_proprio_enregistrer_config($datas)) {
		return array('message_ok' => _T('spipproprio:ok_config'));
	}

	return array('message_erreur' => _T('spipproprio:erreur_config'));
}
