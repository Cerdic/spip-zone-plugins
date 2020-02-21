<?php

/**
 * Fonctions spécifiques à une saisie
 *
 * @package SPIP\Saisies\selection_multiple
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
function selection_multiple_valeurs_acceptables($valeur, $description) {
	if (!is_array($valeur)) {
		if ($valeur) {
			$valeur = explode(" ", $valeur);
		} else {
			$valeur = array();
		}
	}
	$data = saisies_aplatir_tableau(saisies_trouver_data($description, true));
	if (isset($valeur['choix_alternatif']) and isset($description['options']['choix_alternatif']) and $description['options']['choix_alternatif'] == 'on') {
		unset ($valeur['choix_alternatif']);
	}
	if (saisies_verifier_gel_saisie($description) and isset($description['options']['defaut'])) {
		// Si valeur gelée, on vérifie qu'il n'y ni plus ni moins dans ce qui a été postée
		$defaut = saisies_valeur2tableau($description['options']['defaut']);
		$intersection = array_intersect($defaut, $valeur);
		// L'intersection doit avoir le même nombre de valeur que le défaut. S'il a moins, c'est qu'on supprimé des valeurs, ou renommé
		// L'intersection doit avoir le même nombre de valeur que posté. S'il y en a moins, c'est qu'on a posté de nouvelle valeur
		// Sinon c'est bon
		if (count($intersection) != count($defaut)) {
			return false;
		} elseif (count($intersection) != count($valeur)) {
			return false;
		} else {
			return true;
		}
	} else {
		//A-t-on essayé des poster des valeurs supplémentaires?
		$choix_possibles = array_keys($data, true);
		if (isset($description['options']['disable_choix'])) {
			$disable_choix = explode(',', $description['options']['disable_choix']);
			$choix_possibles = array_diff($choix_possibles, $disable_choix);
		}
		$diff = array_diff($valeur, $choix_possibles);
		if (count($diff)) {
			return false;
		}
	}
	return true;
}

