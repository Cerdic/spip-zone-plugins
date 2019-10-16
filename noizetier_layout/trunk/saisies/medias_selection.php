<?php

/**
 * Fonctions spécifiques à une saisie
 *
 * @package SPIP\Saisies\checkbox
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
 */
function medias_selection_valeurs_acceptables($valeur, $description) {
	include_spip('saisies/selection');
	return selection_valeurs_acceptables($valeur, $description);
}
