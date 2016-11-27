<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Récupérer les fonds utilisés pour styliser le site
 *
 * @pipeline recuperer_fond
 *
 * @param  array $flux Données du pipeline
 *
 * @return array       Données du pipeline
 */
function decomposer_recuperer_fond($flux) {

	$squelettes = array();
	$squelettes[] = $flux['data']['source'];

	if (!isset($GLOBALS['decomposer'])) {
		$GLOBALS['decomposer'] = array();
	}
	$GLOBALS['decomposer'] = array_merge($GLOBALS['decomposer'], $squelettes);

	$GLOBALS['decomposer'] = array_filter($GLOBALS['decomposer']);
	$GLOBALS['decomposer'] = array_unique($GLOBALS['decomposer']);
	$GLOBALS['decomposer'] = array_values($GLOBALS['decomposer']);

	return $flux;
}