<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_metas_charger_dist() {
	// Contexte du formulaire.
	$contexte = array();

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
function formulaires_configurer_metas_verifier_dist() {
	$erreurs = array();

	return $erreurs;
}

function formulaires_configurer_metas_traiter_dist() {
	//Traitement du formulaire.
	$spip_metas = array(
		'spip_metas_title',
		'spip_metas_description',
		'spip_metas_keywords',
		'spip_metas_mots_importants',
	);
	include_spip('inc/config');
	include_spip('inc/meta');
	$message = false;

	/**
	 * On compare les nouvelles saisies aux anciennes. Et si on a une différence, on enregistre cette nouvelle valeur.
	 */
	foreach ($spip_metas as $meta_perso) {
		if (_request($meta_perso) != _request($meta_perso . '_old')) {
			ecrire_meta($meta_perso, _request($meta_perso));
			$message = true;
		}
	}

	// Donnée de retour.
	if ($message) {
		return array(
			'editable' => true,
			'message_ok' => _T('info_modification_enregistree'),
		);
	} else {
		return array('message_ok' => _T('metas:pas_de_modification'));
	}
}
