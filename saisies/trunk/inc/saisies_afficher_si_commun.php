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

/**
 * Retourne le résultat de l'évaluation d'un plugin actif
 * @param string $champ (sans les @@)
 * @return bool '' ('' si jamais on ne teste pas un plugin)
**/
function saisies_afficher_si_evaluer_plugin($champ) {
	if (preg_match_all('#plugin:(.*)#', $champ, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $plugin) {
			$plugin_a_tester = $plugin[1];
			$actif = test_plugin_actif($plugin_a_tester);
		}
	}	else {
		$actif = '';
	}
	return $actif;
}
/**
 * Retourne la valeur d'une config, si nécessaire
 * @param string $champ le "champ" a tester : config:xxx ou un vrai champ
 * @return string
**/
function saisies_afficher_si_get_valeur_config($champ) {
	$valeur = '';
	if (preg_match("#config:(.*)#", $champ, $config)) {
		$config_a_tester = str_replace(":", "/", $config[1]);
		$valeur = lire_config($config_a_tester);
	}
	return $valeur;
}
