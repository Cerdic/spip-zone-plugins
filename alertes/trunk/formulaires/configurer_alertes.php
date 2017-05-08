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
		'activer_alertes' => (_request('activer_alertes')) ? _request('activer_alertes') : (isset($a['activer_alertes']) ? $a['activer_alertes'] : ''),
		'activer_alertes_articles' => (_request('activer_alertes_articles')) ? _request('activer_alertes_articles') : (isset($a['activer_alertes_articles']) ? $a['activer_alertes_articles'] : ''),
		'groupes' => (_request('groupes')) ? _request('groupes') : (isset($a['groupes']) ? $a['groupes'] : ''),
		'secteurs' => (_request('secteurs')) ? _request('secteurs') : (isset($a['secteurs']) ? $a['secteurs'] : ''),
		'rubriques' => (_request('rubriques')) ? _request('rubriques') : (isset($a['rubriques']) ? $a['rubriques'] : ''),
		'auteurs' => (_request('auteurs')) ? _request('auteurs') : (isset($a['auteurs']) ? $a['auteurs'] : ''),
		'mode_envoi' => (_request('mode_envoi')) ? _request('mode_envoi') : (isset($a['mode_envoi']) ? $a['mode_envoi'] : ''),
		'intervalle_cron' => (_request('intervalle_cron')) ? _request('intervalle_cron') : (isset($a['intervalle_cron']) ? $a['intervalle_cron'] : ''),
		'nb_mails' => (_request('nb_mails')) ? _request('nb_mails') : (isset($a['nb_mails']) ? $a['nb_mails'] : ''),
	);

	return $valeurs;
}

function formulaires_configurer_alertes_verifier_dist() {
	$erreurs = array();
	//Champs obligatoires
	foreach (array('activer_alertes', 'mode_envoi', 'nb_mails') as $champ) {
		if (!_request($champ)) {
			$erreurs[$champ] = _T('alerte:required_field');
		}
	}
	//Message d'erreur générique
	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('alerte:generic_error');
	}

	return $erreurs;
}

function formulaires_configurer_alertes_traiter_dist() {
	//Récuperation de la nouvelle configuration et serialization
	$a = serialize(array(
		'activer_alertes' => _request('activer_alertes'),
		'activer_alertes_articles' => _request('activer_alertes_articles'),
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
	$res = array('message_ok' => _T('alerte:alerts_configuration_message_ok'));

	return $res;
}

