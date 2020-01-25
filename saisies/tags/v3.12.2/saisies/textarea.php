<?php

/**
 * Fonctions spécifiques à une saisie
 *
 * @package SPIP\Saisies\textarea
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
function textarea_valeurs_acceptables($valeur, $description) {
	$options = $description['options'];
	if (
		saisies_verifier_gel_saisie($description)
		and
		isset($options['defaut'])
		and $options['defaut'] != $valeur
	) {
		return false;
	}
	return true;
}

