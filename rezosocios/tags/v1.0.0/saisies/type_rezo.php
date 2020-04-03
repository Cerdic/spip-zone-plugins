<?php
/**
 * Fonctions spécifiques à une saisie
 *
 * @package SPIP\Saisies\type_rezo
 */


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
function type_rezo_valeurs_acceptables($valeur, $description) {

	if (!empty($description['options']['multiple'])) {
		include_spip('saisies/selection_multiple');
		$fonction = selection_multiple_valeurs_acceptables();
	} else {
		include_spip('saisies/selection');
		$fonction = selection_valeurs_acceptables();
	}

	return $fonction($valeur, $description);
}
