<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



function suploadhtml5_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/saisie_upload.css').'" type="text/css" media="screen" />';

	return $flux;
}

/**
 * Lacher le cron de nettoyage des fichiers media temporaire toute les 24 heures
 *
 * @param mixed $taches
 * @access public
 * @return mixed
 */
function suploadhtml5_taches_generales_cron($taches) {
	$taches['nettoyer_document_temporaire'] = 24*3600;
	return $taches;
}

/**
 * Permet de vérifier une saisie uploadhtml5
 *
 * @param array $flux
 * @access public
 * @return array
 */
function suploadhtml5_formulaire_verifier($flux) {

	include_spip('inc/saisies');

	// Est-ce que le formulaire soumis possède des saisies upload ?
	$form = $flux['args']['form'];
	// Ce n'est pas une faute de frappe
	// le pipeline renvoi les argument dans un double args
	$form_args = $flux['args']['args'];
	$saisies = saisies_chercher_formulaire($form, $form_args);

	// S'il n'y a pas de saisies, il n'y a rien à vérifier
	if (!$saisies) {
		return $flux;
	}

	// Chercher si une saisie upload ce trouve dans le tableau
	include_spip('inc/saisie_upload');
	$saisie = chercher_saisie_upload($saisies);

	// Une saisie upload obligatoire a été trouvée,
	// il faut donc la vérifier
	if (isset($saisie['options']['obligatoire'])) {
		// On commence par supprimer l'erreur générique.
		// Comme la dropzone n'est pas un <input> classique,
		// l'erreur générique sera toujours présente.
		unset($flux['data'][$saisie['options']['nom']]);

		// On vérifie qu'il y a des documents dans la session
		include_spip('inc/saisie_upload');
		$documents = saisie_upload_get();

		// Pas de document dans la session ?
		if (empty($documents['document'])) {
			// Erreur !
			$flux['data'][$saisie['options']['nom']] = _T('info_obligatoire');
		}

		// On vérifie le nombre d'erreur pour savoir
		// s'il faut garder message_erreur
		if (count($flux['data']) == 1) {
			// une seul erreur, c'est message_erreur qui est seul.
			unset($flux['data']['message_erreur']);
		}
	}

	return $flux;
}
