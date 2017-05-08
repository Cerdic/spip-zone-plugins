<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/abstract_sql');
include_spip('inc/config');

function formulaires_configurer_socicon_charger_dist() {

	$contexte = array(
		'socicon' => '',
	);
	$socicon_config = lire_config('socicon');
	//$socicon_config = unserialize($socicon_config);
	if (is_array($socicon_config) and isset($socicon_config) and is_array($socicon_config)) {
		$contexte['socicon'] = $socicon_config;
	} else {
		$contexte['socicon'] = array('facebook','twitter','instagram','googleplus','blogger','pinterest','linkedin','youtube','rss','mail','tripadvisor','vimeo','flickr');
	}
	$socicon_request = _request('socicon');
	if (is_array($socicon_request) and is_array($socicon_request)) {
		foreach ($socicon_request as $key => $value) {
			$contexte['socicon'] = $socicon_request;
		}
	}

	// Contexte du formulaire.

	return $contexte;
}

/*
*   Fonction de vérification, cela fonction avec un tableau d'erreur.
*   Le tableau est formater de la sorte:
*   if (!_request('NomErreur')) {
*       $erreurs['message_erreur'] = '';
*       $erreurs['NomErreur'] = '';
*   }
*   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
*/
function formulaires_configurer_socicon_verifier_dist() {
	$errors = array();
	// array_count_values
	return $errors;
}

function formulaires_configurer_socicon_traiter_dist() {
	//Traitement du formulaire.
	$socicon_config = lire_config('socicon');
	$socicon = _request('socicon');
	if (is_array($socicon)) {
		$socicon_config = $socicon;
	}
	ecrire_config('socicon', serialize($socicon_config));

	// Donnée de retour.
	return array(
		'editable' => true,
		'message_ok' => _T('config_info_enregistree'),
		'redirect' => '',
	);
}
