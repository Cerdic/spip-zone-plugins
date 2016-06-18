<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_facebook_publier_saisies_dist($objet, $id_objet) {
	include_spip('inc/facebook');
	$saisies = array(
		array(
			'saisie' => 'selection',
			'options' => array(
				'nom' => 'page',
				'label' => _T('facebook:page'),
				'cacher_option_intro' => 'oui',
				'datas' => facebook_saisie_pages()
			)
		)
	);
	return $saisies;
}

function formulaires_facebook_publier_traiter_dist($objet, $id_objet) {

	include_spip('inc/facebook');
	$lien = generer_url_entite_absolue($id_objet, $objet);

	include_spip('inc/texte');
	include_spip('inc/filtres');
	$message = generer_info_entite($id_objet, $objet, 'titre');

	if (_request('page')) {
		$erreur = facebook_poster_lien_page(
			$lien,
			$message,
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
