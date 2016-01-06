<?php

/**
 * Utilisations de pipelines par Unsplash.
 *
 * @plugin     Unsplash
 *
 * @copyright  2015-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout de liste sur la vue d'un auteur.
 *
 * @pipeline affiche_auteurs_interventions
 *
 * @param array $flux Données du pipeline
 *
 * @return array Données du pipeline
 */
function unsplash_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/unsplash', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('unsplash:info_unsplash_auteur'),
		), array('ajax' => true));
	}

	return $flux;
}

function unsplash_header_prive($flux) {
	$flux .= "<link rel='stylesheet' id='font-awesome-css'  href='" . find_in_path('lib/font-awesome/css/font-awesome.min.css') . "' type='text/css' media='all' />";

	return $flux;
}
