<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Configuration des elements "abonnables".
 */
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}
include_spip('inc/utils');

function formulaires_configurer_alertes_charger_dist() {
	//Recuperation de la configuration préalable
	include_spip('inc/config');
	$a = lire_config('config_alertes');
	if (!is_array($a)) {
		$a = array();
	}
	// Chargement de la configuration
	$valeurs = array(
		'activer_alertes' => (_request('activer_alertes')) ? _request('activer_alertes') : $a['activer_alertes'],
		'groupes' => (_request('groupes')) ? _request('groupes') : $a['groupes'],
		'secteurs' => (_request('secteurs')) ? _request('secteurs') : $a['secteurs'],
		'rubriques' => (_request('rubriques')) ? _request('rubriques') : $a['rubriques'],
		'auteurs' => (_request('auteurs')) ? _request('auteurs') : $a['auteurs'],
		'mode_envoi' => (_request('mode_envoi')) ? _request('mode_envoi') : $a['mode_envoi'],
		'intervalle_cron' => (_request('intervalle_cron')) ? _request('intervalle_cron') : $a['intervalle_cron'],
		'nb_mails' => (_request('nb_mails')) ? _request('nb_mails') : $a['nb_mails'],
	);

	return $valeurs;
}

function formulaires_configurer_alertes_verifier_dist() {
	$erreurs = array();
	//Champs obligatoires
	foreach (array('activer_alertes', 'mode_envoi', 'nb_mails') as $champ) {
		if (!_request($champ)) {
			$erreurs[$champ] = _T('alertes:required_field');
		}
	}
	//Message d'erreur générique
	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('alertes:generic_error');
	}

	return $erreurs;
}

function formulaires_configurer_alertes_traiter_dist() {
	//Récuperation de la nouvelle configuration et serialization
	$a = serialize(array(
		'activer_alertes' => _request('activer_alertes'),
		'groupes' => _request('groupes'),
		'secteurs' => _request('secteurs'),
		'rubriques' => _request('rubriques'),
		'auteurs' => _request('auteurs'),
		'mode_envoi' => _request('mode_envoi'),
		'intervalle_cron' => _request('intervalle_cron'),
		'nb_mails' => _request('nb_mails'),
	));
	//Sauvegarde dans les meta
	include_spip('inc/meta');
	ecrire_meta('config_alertes', $a);
	$res = array('message_ok' => _T('alertes:alerts_configuration_message_ok'));

	return $res;
}

