<?php

/**
 * Fonctions spécifiques à une saisie
 *
 * @package SPIP\Saisies\date
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
function date_valeurs_acceptables($valeur, $description) {
	if (saisies_verifier_gel_saisie($description) and isset($description['options']['defaut'])) {
		$defaut = $description['options']['defaut'];
		include_spip('inc/filtres');
		$defaut = recup_date($defaut);
		$valeur = recup_date($valeur);
		foreach ($valeur as &$element) {
			if ($element === 0) {
				$element = '00';
			}
		}
		foreach ($defaut as &$element) {
			if ($element === 0) {
				$element = '00';
			}
		}
		if (array_diff_assoc($defaut, $valeur)) {
			return false;
		}
	}
	return true;
}

