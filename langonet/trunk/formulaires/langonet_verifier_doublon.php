<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_langonet_verifier_doublon_charger() {

	include_spip('inc/outiller');
	$modules_fr = lister_modules('fr');

	$defaut_modules = _request('defaut_modules');
	$modules = _request('modules');
	if (($defaut_modules == 'oui')
	OR (!$defaut_modules AND !$modules)) {
		$modules_choisis = array_keys($modules_fr);
		$defaut_modules = 'oui';
	}
	else {
		$modules_choisis = array();
		foreach (_request('modules') as $_valeurs) {
			$m = explode(':', $_valeurs);
			$modules_choisis[] = reset($m);
		}
	}

	return array('_modules' => $modules_fr,
				'_modules_choisis' => $modules_choisis,
				'defaut_modules' => $defaut_modules,
				'verification' => _request('verification'));
}

function formulaires_langonet_verifier_doublon_verifier() {
	$erreurs = array();

	$obligatoires = array();
	if (!_request('defaut_modules'))
		$obligatoires[] = 'modules';
	if ($obligatoires) {
		foreach ($obligatoires as $_champ) {
			if (!_request($_champ)) {
				$erreurs[$_champ] = _T('langonet:message_nok_champ_obligatoire');
			}
		}
	}

	return $erreurs;
}

function formulaires_langonet_verifier_doublon_traiter() {

	// Recuperation des champs du formulaire
	$verification = _request('verification');

	$modules = array();
	if (_request('defaut_modules') == 'oui') {
		include_spip('inc/outiller');
		$modules_fr = lister_modules('fr');
		foreach ($modules_fr as $_module => $_fichier) {
			$modules[] = "${_module}:${_fichier}";
		}
	}
	else {
		$modules = _request('modules');
	}

	// Verification et formatage des resultats de la recherche
	$retour = array();
	$verifier_doublon = charger_fonction('verifier_doublon','inc');
	$resultats = $verifier_doublon($verification, $modules);
	if (isset($resultats['erreur'])) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour['message_ok']['resume'] = _T('langonet:message_ok_doublons');
		$retour['message_ok']['total'] = $resultats['total'];
		$retour['message_ok']['doublons'] = $resultats['doublons'];
	}
	$retour['editable'] = true;
	return $retour;
}

?>