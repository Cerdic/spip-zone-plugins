<?php
/**
 * API de vérification : vérification de la validité d'un attribut class d'une balise HTML.
 *
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérifie la validité d'un attribut class d'une balise HTML.
 *
 * @param string $valeur
 *        La chaine à vérifier.
 * @param array $options
 *        Aucune option possible.
 *
 * @return string
 *         Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier_attribut_class_dist($valeur, $options = array()) {

	// On initialise la sortie avec le message d'erreur.
	$erreur = _T('verifier:erreur_attribut_class');

	if (is_string($valeur) and preg_match('#^([\w\s-]+)$#i', $valeur)) {
		// La saisie est valide, on renvoie la chaine vide.
		$erreur = '';
	}

	return $erreur;
}
