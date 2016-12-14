<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérifie les passwords
 * Options :
 *
 * - match : Nom du champ avec lequel le password dois correspondre
 * - longueur : taille minimum du password. Si la longueur a pour valeur "spip",
 * c'est la constante _PASS_LONGUEUR_MINI qui sera utilisée
 *
 * @param string $valeur
 * @param array $options
 * @access public
 * @return string
 */
function verifier_password_dist($valeur, $options = array()) {
	$erreur = '';

	// Si on demander la longueur de password définie par SPIP
	if ($options['longueur'] == 'spip') {
		$options['longueur'] = _PASS_LONGUEUR_MINI;
	}

	// Vérification de la longueur
	if (strlen($valeur) < $options['longueur']) {
		$erreur = _T('info_passe_trop_court_car_pluriel', array('nb' => $options['longueur']));
	}

	// Vérification de la correspondance entre les passwords
	if ($match = $options['match'] and _request($match) != $valeur) {
		$erreur = _T('info_passes_identiques');
	}

	return $erreur;
}
