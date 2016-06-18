<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_facebook_poster_saisies_dist() {

	include_spip('inc/facebook');

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
		),
		array(
			'saisie' => 'selection',
			'options' => array(
				'nom' => 'page',
				'cacher_option_intro' => 'oui',
				'label' => _T('facebook:page'),
				'datas' => facebook_saisie_pages()
			)
		)
	);

	return $saisies;
}

function formulaires_facebook_poster_traiter_dist() {
	//Traitement du formulaire.
	include_spip('inc/facebook');

	if (_request('page')) {
		$erreur = facebook_poster_lien_page(
			_request('facebook_lien'),
			_request('facebook_message'),
			_request('page')
		);
	}

	if ($erreur) {
		return array('message_erreur' => $erreur);
	}

	// Donnée de retour.
	return array(
		'editable' => true,
		'message_ok' => _T('facebook:confirmer_poster')
	);
}
