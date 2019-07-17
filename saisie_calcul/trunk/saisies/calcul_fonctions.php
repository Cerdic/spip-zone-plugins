<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Transforme une expression de calcul entrée par l'utilisateur/trice en expression interprétable en JS
 * @param string $expr
 * @return string
**/
function saisie_calcul_2_js($expr) {
	$expr = saisie_calcul_securiser($expr);
	$expr = preg_replace("#@(.*)@#U", "$('#champ_$1').val()", $expr);
	return $expr;
}

/**
 * Sécurise une expression de calcul passée en paramètre.
 * N'autorise que :
 * - texte entre @@
 * - opérateurs des opérations de base
 * - parenthèses
 * - point, virgule
 * - nombre
 * @param string $expr
 * @return string $expr soit l'expression, soit rien si jamais cela ne respect pas les règles
**/
function saisie_calcul_securiser($expr) {
	$hors_arobase = "#("
		."\d|"
		."\(\)|"
		."\+|"
		."\*|"
		."\/|"
		."-|"
		."\.|"
		.",|"
		.")#";
	$arobase = "#@.*@#U";
	$valable = preg_replace($hors_arobase,'',$expr);
	$valable = preg_replace($arobase,'',$valable);
	if (!trim($valable)) {//Si à la fin il ne reste plus rien, c'est que c'est bon, on retourne donc l'expression
		return $expr;
	} else{//Sinon c'est le mal, et on retourne le vide
		return '';
	}
	return $expr;
}
