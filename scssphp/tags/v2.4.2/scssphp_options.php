<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// On active le calcul si var_mode=css
if (_request('var_mode') == 'css') {
	define('_VAR_MODE', 'calcul');
}

// Activer les _SCSS_SOURCE_MAP si besoin
// Pour le moment seul les sourcemaps inline sont gérés
// http://leafo.github.io/scssphp/docs/#source-maps
// define('_SCSS_SOURCE_MAP', true);


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