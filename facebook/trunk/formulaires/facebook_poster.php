<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_facebook_poster_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'facebook_message',
				'label' => _T('facebook:message')
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'facebook_lien',
				'label' => _T('facebook:lien')
			)
		)
	);
	return $saisies;
}

function formulaires_facebook_poster_traiter_dist() {
	//Traitement du formulaire.
	include_spip('inc/facebook');
	$erreur = facebook_poster_lien(
		_request('facebook_lien'),
		_request('facebook_message')
	);

	if ($erreur) {
		return array('message_erreur' => $erreur);
	}

	// Donnée de retour.
	return array(
		'editable' => true,
		'message_ok' => _T('facebook:confirmer_poster')
	);
}
