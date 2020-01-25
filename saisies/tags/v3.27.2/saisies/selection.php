<?php

/**
 * Fonctions spécifiques à une valeur
 *
 * @package SPIP\valeurs\selection
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
function selection_valeurs_acceptables($valeur, $description) {
	$options = $description['options'];
	if ($valeur == '' and !isset($options['obligatoire'])) {
		return true;
	}
	if (saisies_verifier_gel_saisie($description) and isset($options['defaut'])) {
		return $valeur == $options['defaut'];
	} else {
		$data = saisies_trouver_data($description, true);
		$data = saisies_aplatir_tableau($data);
		$data = array_keys($data);
		if (isset($options['disable_choix'])) {
			$disable_choix = explode(',', $options['disable_choix']);
			$data = array_diff($data, $disable_choix);
		}
		return (in_array($valeur ,$data));
	}
}

