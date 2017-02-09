<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * les boutons d'administration : ajouter le bouton var_mode=css
 *
 * @pipeline formulaire_admin
 * @param array $flux
 * @return $flux
 */
function scssphp_formulaire_admin($flux) {
	if (autoriser('configurer', 'scssphp')) {
		$btn = recuperer_fond('prive/bouton/calculer_css');
		$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn.'$1', $flux['data']);
	}

	return $flux;
}