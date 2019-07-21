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
	$expr = preg_replace("#@(.*)@#U", "Number($('.editer:not(.afficher_si_masque) [name=&quot;$1&quot;]').val()||0)", $expr);
	$expr = str_replace(",",".",$expr);// virgule -> point
	$expr = str_replace("ROUND","Math.round",$expr);
	$expr = str_replace("\n",'',$expr);
	$expr = str_replace("\r",'',$expr);
	return $expr;
}


/**
 * Transforme une expression de calcul entrée par l'utilisateur/trice en expression interprétable en JS
 * @param string $expr
 * @return string
**/
function saisie_calcul_2_php($expr) {
	$expr = saisie_calcul_securiser($expr);
	$expr = preg_replace("#@(.*)@#U", 'floatval(_request(\'$1\'))', $expr);
	$expr = str_replace(",",".",$expr);// virgule -> point
	$expr = str_replace("ROUND","saisie_calcul_arrondi",$expr);
	return $expr;
}
/**
 * Une fonction d'arrondi php qui imite la fonction d'arrondi JS
 * @param int $valeur
 * @param int
**/
function saisie_calcul_arrondi($valeur) {
	if ($valeur > 0) {
		return round($valeur, 0, PHP_ROUND_HALF_UP);
	} else {
		return round($valeur, 0, PHP_ROUND_HALF_DOWN);
	}
}

/**
 * Sécurise une expression de calcul passée en paramètre.
 * N'autorise que :
 * - texte entre @@
 * - opérateurs des opérations de base
 * - parenthèses
 * - point, virgule
 * - nombre
 * - ROUND (pour l'arrondi)
 * @param string $expr
 * @return string $expr soit l'expression, soit rien si jamais cela ne respect pas les règles
**/
function saisie_calcul_securiser($expr) {
	$hors_arobase = "#("
		."\d|"
		."\(|\)|"
		."\+|"
		."\*|"
		."\/|"
		."-|"
		."\.|"
		."\,|"
		."ROUND|"
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
