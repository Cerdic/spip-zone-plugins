<?php
/**
 * API de vérification : vérification de la validité d'un test afficher_si
 *
 * @plugin     saisies
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
/**
 *
 *
 *
 *
 * @param string $valeur
 *   La valeur à vérifier.
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier_afficher_si_dist($valeur) {
	include_spip('inc/saisies_afficher_si_commun');
	$erreur = _T('saisies:erreur_syntaxe_afficher_si');
	$tests = saisies_parser_condition_afficher_si($valeur);
	if (!saisies_afficher_si_verifier_syntaxe($valeur, $tests)) {
		return $erreur;
	} else {
		return '';
	}
}
