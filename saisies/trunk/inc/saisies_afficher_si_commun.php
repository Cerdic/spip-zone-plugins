<?php

/**
 * Gestion de l'affichage conditionnelle des saisies.
 * Partie commun js/php
 *
 * @package SPIP\Saisies\Afficher_si_commun
 **/


/**
 * Reçoit une condition
 * la parse pour trouver champs/opérateurs/valeurs etc.
 * @param string $condition
 * @return array tableau d'analyse (resultat d'un preg_match_all) montrant sous condition par sous condition l'analyse en champ/opérateur/valeur etc.
**/
function saisies_parser_condition_afficher_si($condition) {
	$regexp =
	  "(?<negation>!?)" // négation éventuelle
		. "(?:@(?<champ>.+?)@)" // @champ_@
		. "(" // partie operateur + valeur (optionnelle) : debut
		. "(?:\s*?)" // espaces éventuels après
		. "(?<operateur>==|!=|IN|!IN|>=|>|<=|<)" // opérateur
		. "(?:\s*?)" // espaces éventuels après
		. "((?<guillemet>\"|')(?<valeur>.*?)(\k<guillemet>)|(?<valeur_numerique>\d+))" // valeur (string) ou valeur_numérique (int)
		. ")?"; // partie operateur + valeur (optionnelle) : fin
	$regexp = "#$regexp#";
	preg_match_all($regexp, $condition, $tests, PREG_SET_ORDER);
	return $tests;
}
