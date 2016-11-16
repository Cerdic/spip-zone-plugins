<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function formulaires_gerer_savecfg_charger_dist() {
	$valeurs = array(
		'nom' => '',
		'fondcfg' => _request('cfg'),
		'fond_id' => ''
	);

	return $valeurs;
}

function formulaires_gerer_savecfg_verifier_dist() {
	$erreurs = array();
	if (_request('fond_id') == 'none') {
		$erreurs['message_erreur'] = _T('spip:info_obligatoire');
	}

	return $erreurs;
}

function formulaires_gerer_savecfg_traiter_dist() {
	if (_request('_restaurer_')) {
		$message = restaurer_savecfg(_request('fond_id'));
	}
	if (_request('_supprimer_')) {
		$message = supprimer_savecfg(_request('fond_id'));
	}

	return $message;
}

function restaurer_savecfg($id_savecfg) {
	$res = array();
	if (sql_countsel('spip_savecfg', 'fond=' . sql_quote(_request('cfg'))) > 0) {
		include_spip('inc/meta');
		$sfg = sql_fetsel(array('titre', 'valeur'), 'spip_savecfg', 'id_savecfg=' . sql_quote($id_savecfg));
		ecrire_meta(_request('cfg'), $sfg['valeur']);
		ecrire_metas();
	}
	$res['message_ok'] = _T('savecfg:savecfg_restauree', array('nom' => $sfg['titre'], 'fond' => _request('cfg')));

	return $res;
}

function supprimer_savecfg($id_savecfg) {
	$res = array();
	$nom = sql_getfetsel('titre', 'spip_savecfg',
		'id_savecfg=' . sql_quote($id_savecfg) . ' AND fond=' . sql_quote(_request('cfg')));
	sql_delete('spip_savecfg', 'id_savecfg=' . sql_quote($id_savecfg));
	$res['message_ok'] = _T('savecfg:savecfg_supprimee', array('nom' => $nom, 'fond' => _request('cfg')));

	return $res;
}
