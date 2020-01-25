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
	  '(?<negation>!?)' // négation éventuelle
		. '(?:@(?<champ>.+?)@)' // @champ_@
		. '(?<total>:TOTAL)?' // TOTAL éventuel (pour les champs de type case à cocher)
		. '(' // partie operateur + valeur (optionnelle) : debut
		. '(?:\s*?)' // espaces éventuels après
		. '(?<operateur>==|!=|IN|!IN|>=|>|<=|<)' // opérateur
		. '(?:\s*?)' // espaces éventuels après
		. '((?<guillemet>"|\')(?<valeur>.*?)(\k<guillemet>)|(?<valeur_numerique>\d+))' // valeur (string) ou valeur_numérique (int)
		. ')?' // partie operateur + valeur (optionnelle) : fin
		. '|(?<booleen>false|true)';//accepter false/true brut
	$regexp = "#$regexp#";
	preg_match_all($regexp, $condition, $tests, PREG_SET_ORDER);
	return $tests;
}

/**
 * Retourne le résultat de l'évaluation d'un plugin actif
 * @param string $champ (sans les @@)
 * @param string $negation ! si on doit nier
 * @return bool '' ('' si jamais on ne teste pas un plugin)
**/
function saisies_afficher_si_evaluer_plugin($champ, $negation = '') {
	if (preg_match_all('#plugin:(.*)#', $champ, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $plugin) {
			$plugin_a_tester = $plugin[1];
			if ($negation) {
				$actif = !test_plugin_actif($plugin_a_tester);
			} else {
				$actif = test_plugin_actif($plugin_a_tester);
			}
		}
	}	else {
		$actif = '';
	}
	return $actif;
}


/**
 * Teste une condition d'afficher_si
 * @param string|array champ le champ à tester. Cela peut être :
 *	- un string
 *	- un tableau
 *	- un tableau sérializé
 * @param string $total TOTAL si on demande de faire le décompte dans un tableau
 * @param string $operateur : l'opérateur:
 * @param string $valeur la valeur à tester pour un IN. Par exemple "23" ou encore "23", "25"
 * @param string $negation y-a-t-il un négation avant le test ? '!' si oui
 * @return bool false / true selon la condition
**/
function saisies_tester_condition_afficher_si($champ, $total, $operateur=null, $valeur=null, $negation = '') {
	// Si operateur null => on test juste qu'un champ est cochée / validé
	if ($operateur === null and $valeur === null) {
		if ($negation) {
			return !(isset($champ) and $champ);
		}
		else {
			return isset($champ) and $champ;
		}
	}

	if (is_null($champ)) {
		$champ = '';
	}
	// Dans tous les cas, enlever les guillemets qui sont au sein de valeur
	//Si champ est de type string, tenter d'unserializer
	$tenter_unserialize = @unserialize($champ);
	if ($tenter_unserialize)  {
		$champ = $tenter_unserialize;
	}

	//Et maintenant appeler les sous fonctions qui vont bien
	if (is_string($champ)) {
		$retour = saisies_tester_condition_afficher_si_string($champ, $operateur, $valeur);
	} elseif (is_array($champ)) {
		$retour = saisies_tester_condition_afficher_si_array($champ, $total, $operateur, $valeur);
	} else {
		$retour = false;
	}
	if ($negation) {
		return !$retour;
	} else {
		return $retour;
	}
}

/**
 * Teste un condition d'afficher_si lorsque la valeur envoyée par le formulaire est une chaîne
 * @param string champ le champ à tester.
 * @param string $operateur : l'opérateur:
 * @param string|int $valeur la valeur à tester pour un IN. Par exemple "23" ou encore "23, 25", 23
 * @return bool false / true selon la condition
**/
function saisies_tester_condition_afficher_si_string($champ, $operateur, $valeur) {
	if ($operateur == "==") {
		return $champ == $valeur;
	} elseif ($operateur == "!=") {
		return $champ != $valeur;
	} elseif ($operateur == '<') {
		return $champ < $valeur;
	} elseif ($operateur == '<=') {
		return $champ <= $valeur;
	} elseif ($operateur == '>') {
		return $champ > $valeur;
	} elseif ($operateur == '>=') {
		return $champ >= $valeur;
	} else {//Si mauvaise operateur -> on annule
		return false;
	}
}

/**
 * Teste un condition d'afficher_si lorsque la valeur postée est  un tableau
 * @param array champ le champ à tester.
 * @param string $operateur : l'opérateur:
 * @param string $valeur la valeur à tester pour un IN. Par exemple "23" ou encore "23", "25"
 * @return bool false / true selon la condition
**/
function saisies_tester_condition_afficher_si_array($champ, $total, $operateur, $valeur) {
	if ($total) {//Cas 1 : on demande à compter le nombre total de champ
		return saisies_tester_condition_afficher_si_string(count($champ), $operateur, $valeur);
	} else {//Cas deux : on test une valeur
		$valeur = explode(',', $valeur);
		$intersection = array_intersect($champ, $valeur);
		if ($operateur == "==" or $operateur == "IN") {
			return count($intersection) > 0;
		} else {
			return count($intersection) == 0;
		}
		return false;
	}
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

/** Vérifie qu'une condition est sécurisée
 * IE : ne permet pas d'executer n'importe quel code arbitraire.
 * @param string $condition
 * @param array $tests tableau des tests parsés
 * @return bool true si secure / false sinon
 **/
function saisies_afficher_si_secure($condition, $tests=array()) {
	$condition_original = $condition;
	$hors_test = array('||','&&','!','(',')','true','false');
	foreach ($tests as $test) {
		$condition = str_replace($test[0], '', $condition);
	}
	foreach ($hors_test as $hors) {
		$condition = str_replace($hors, '', $condition);
	}
	$condition = trim($condition);
	if ($condition) {// il reste quelque chose > c'est le mal
		spip_log("Afficher_si incorrect. $condition_original non sécurisée", "saisies"._LOG_CRITIQUE);
		return false;
	} else {
		return true;
	}
}

/** Vérifie qu'une condition respecte la syntaxe formelle
 * @param string $condition
 * @param array $tests liste des tests simples
* @return bool
**/
function saisies_afficher_si_verifier_syntaxe($condition, $tests=array()) {
	if ($tests and saisies_afficher_si_secure($condition, $tests)) {//Si cela passe la sécurité, faisons des tests complémentaires
		// parenthèses équilibrées
		if (substr_count($condition,'(') != substr_count($condition,')')) {
			return false;
		}
		// pas de && ou de || qui traine sans rien à gauche ni à droite
		$condition = " $condition ";
		$condition_pour_sous_test = str_replace('||','$', $condition);
		$condition_pour_sous_test = str_replace('&&','$', $condition_pour_sous_test);
		$liste_sous_tests = explode('$', $condition_pour_sous_test);
		$liste_sous_tests = array_map('trim', $liste_sous_tests);
		if ($liste_sous_tests != array_filter($liste_sous_tests)) {
			return false;
		}

		return true;
	}
	return false;

}
