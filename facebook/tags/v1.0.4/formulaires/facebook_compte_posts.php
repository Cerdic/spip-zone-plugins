<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_facebook_compte_posts_saisies_dist() {
	include_spip('inc/facebook');
	$saisies = array(
		array(
			'saisie' => 'selection',
			'options' => array(
				'defaut' => lire_config(facebook_compte_post),
				'nom' => 'page',
				'label' => _T('facebook:compte_post'),
				'cacher_option_intro' => 'oui',
				'datas' => facebook_saisie_pages()
			)
		)
	);
	return $saisies;
}

function formulaires_facebook_compte_posts_traiter_dist() {

	$id = _request('page');
	$retour = array();
	if (ecrire_config('facebook_compte_post', $id)) {
		$retour = array(
			'message_ok' => _T('facebook:confirmer_enregistrer_compte_post')
		);
	} else {
		$retour = array(
			'message_erreur' => _T('facebook:erreur_enregistrer_compte_post')
		);
	}
	return $retour;

}
