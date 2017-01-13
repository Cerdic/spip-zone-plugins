<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérifie qu'un entier cohérent peut être extrait de la valeur
 * Options :
 * - min : valeur minimale acceptée
 * - max : valeur maximale acceptée
 *
 * @param string $valeur
 *   La valeur à vérifier.
 * @param array $options
 *   Si ce tableau associatif contient une valeur pour 'min' ou 'max', un contrôle supplémentaire sera effectué.
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier_decimal_dist($valeur, $options = array()) {
	$erreur = _T('verifier:erreur_decimal');
	if (isset($options['separateur']) and $options['separateur']!='') {
		$valeur = str_replace($options['separateur'], '.', $valeur);
	}
	// Pas de tableau ni d'objet
	if (is_numeric($valeur) and $valeur == floatval($valeur)) {
		// Si c'est une chaine on convertit en flottant
		$valeur = floatval($valeur);
		$ok = true;
		$erreur = '';

		if (isset($options['min'])) {
			$min = floatval(str_replace(",", '.', $options['min']));
			$ok = ($ok and ($valeur >= $min));
		}

		if (isset($options['max'])) {
			$max = floatval(str_replace(",", '.', $options['max']));
			$ok = ($ok and ($valeur <= $max));
		}

		if (!$ok) {
			if (isset($options['min']) and isset($options['max'])) {
				$erreur = _T('verifier:erreur_entier_entre', $options);
			} elseif (isset($options['max'])) {
				$erreur = _T('verifier:erreur_entier_max', $options);
			} else {
				$erreur = _T('verifier:erreur_entier_min', $options);
			}
		}
	}

	// On vérifie le nombre de décimales après la virgule
	if (isset($options['nb_decimales']) and $nb_decimales = $options['nb_decimales'] and round($valeur, $nb_decimales) != $valeur) {
		$erreur = _T('verifier:erreur_decimal_nb_decimales', array('nb_decimales' => $nb_decimales));
	}

	return $erreur;
}
