<?php

/**
 * Fonctions spécifiques à une saisie
 *
 * @package SPIP\Saisies\case
**/


// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Vérifie que la valeur postée
 * correspond aux valeurs proposées lors de la config de valeur
 * @param string $valeur la valeur postée
 * @param array $description la description de la saisie
 * @return bool true si valeur ok, false sinon,
**/
function case_valeurs_acceptables($valeur, $description) {
	$options = $description['options'];
	$valeur_oui = isset($options['valeur_oui']) ? $options['valeur_oui'] : 'on';
	$valeur_non = isset($options['valeur_non']) ? $options['valeur_non'] : '';
	if (saisies_verifier_gel_saisie($description)) {
		if (isset($options['defaut'])) {
			$defaut = $valeur_oui;
		} else {
			$defaut = $valeur_non;
		}
		return $valeur == $defaut;
	} else {
		return ($valeur == $valeur_oui or $valeur == $valeur_non);
	}
}

