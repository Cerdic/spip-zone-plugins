<?php

/**
 * Gestion de l'affichage conditionnelle des saisies.
 * Partie spécifique js
 *
 * @package SPIP\Saisies\Afficher_si_js
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/saisies_afficher_si_commun');

/**
 * Transforme une condition afficher_si en condition js
 * @param string $condition
 * @return string
**/
function saisies_afficher_si_js($condition) {
	if ($tests = saisies_parser_condition_afficher_si($condition)) {
		foreach ($tests as $test) {
			$expression = $test[0];
			$negation = isset($test['negation']) ? $test['negation'] : '' ;
			$champ = isset($test['champ']) ? $test['champ'] : '' ;
			$operateur = isset($test['operateur']) ? $test['operateur'] : '' ;
			$guillemet = isset($test['guillemet']) ? $test['guillemet'] : '' ;
			$negation = isset($test['negation']) ? $test['negation'] : '';
			$valeur = isset($test['valeur']) ? $test['valeur'] : '' ;
			$valeur_numerique = isset($test['valeur_numerique']) ? $test['valeur_numerique'] : '' ;
			$plugin = saisies_afficher_si_evaluer_plugin($champ, $negation);
			if ($plugin !== '') {
				$condition = str_replace($expression, $plugin ? 'true' : 'false', $condition);
			} elseif (stripos($champ, 'config') !== false) {
				$config = saisies_afficher_si_get_valeur_config($champ);
				$test_modifie = eval('return '.saisies_tester_condition_afficher_si($config, $operateur, $valeur, $negation).';') ? 'true' : 'false';
				$condition = str_replace($expression, $test_modifie, $condition);
			} else { // et maintenant, on rentre dans le vif du sujet : les champs. On délégue cela à une autre fonction
				$condition = str_replace($expression, saisies_afficher_si_js_champ($champ, $operateur, $valeur, $valeur_numerique, $guillemet, $negation), $condition);
			}
		}
	}
	return $condition;
}

/**
 * Génère à partir de l'analyse d'une condition afficher_si le test js, pour les champs
 * @param string $champ
 * @param string $operateur
 * @param string $valeur
 * @param string $valeur_numerique
 * @param string $valeur
 * @param string $guillemet
 * @param string $negation
 * @return string condition_js
**/
function saisies_afficher_si_js_champ($champ, $operateur, $valeur, $valeur_numerique, $guillemet, $negation) {
	// Cas d'une valeur numérique : pour le test js, cela ne change rien, on la passe comme valeur
	if ($valeur_numerique and !$valeur) {
		$valeur = $valeur_numerique;
	}
	// Guillemets : si double, les échapper
	if ($guillemet == '"') {
		$guillemet = '\"';
	}
	// Cas de chekbox => convertir les conditions en test IN (compatibilité historique)
	if (stripos($champ, 'checkbox') !== false) {
		if ($operateur == '==') {
			$operateur = 'IN';
		} elseif ($operateur == '!=') {
			$operateur = '!IN';
		}
	}
	if ($operateur) {
		if ($operateur != 'IN' and $operateur != '!IN') {
			return "$negation\$form().find('[name=$champ]').val() $operateur $guillemet$valeur$guillemet";
		} else {
			// cas des checkbox (au sens saisie @checkbox_xx@) => operateur IN ou !IN
			return saisies_afficher_si_js_IN($champ, $operateur, $valeur, $negation);
		}
	}
}


/**
 * Génère les tests js pour le cas où on a l'operateur IN ou !IN
 * c'est-à-dire, en pratique, pour les checkboxes
 * @param string $champ
 * @param string $operateur
 * @param string $valeur
 * @param string $negation
**/
function saisies_afficher_si_js_IN($champ, $operateur, $valeur, $negation) {
	// La négation de l'opérateur remonte globalement
	if ($operateur == '!IN' and $negation) {
		$negation = '';
	} elseif ($operateur == '!IN') {
		$negation = '!';
	}
	// Spliter la valeur pour trouver toutes les cases qui doivent être cochées (ou pas)
	$valeurs = explode(',', $valeur);
	$valeurs = array_map('saisies_afficher_si_js_IN_individuel', $valeurs, array_fill(0,count($valeurs),$champ));
	$valeurs = implode(' || ', $valeurs);
	return "$negation($valeurs)";
}

/**
 * Génère le sous-test js pour le cas où on a l'operateur IN ou !IN
 * c'est-à-dire, en pratique, pour les checkboxes
 * Par "sous-test js", nous entendons le test pour une valeur précise d'un checkbox
 * Attention : $valeur en premier (car fonction appelée dans un array_map())
 * @param string $valeur
 * @param string $champ
 * @return string
**/
function saisies_afficher_si_js_IN_individuel($valeur, $champ) {
	return "$(form).find(checkbox[name=$champ".'[]'."][value='$valeur']).is(':checked')";
}
