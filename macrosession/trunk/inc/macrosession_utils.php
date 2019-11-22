<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!function_exists('existe_argument_balise')) {
	// prolégomène à interprete_argument_balise
	function existe_argument_balise ($n, $p) {
		return (($p->param) && (!$p->param[0][0]) && (count($p->param[0])>$n));
	};
}

/*
 * Recevant un argument entre quotes (contenant par exemple un nom de filtre)
 * trim_quote enlève les espaces de début et fin *à l'intérieur* des quotes
 * ex : reçoit ' filtre ' (quotes comprises) et renvoie 'filtre'
*/
function trim_quote($f) {
	$f = trim($f);	// c'est pas ça l'important
	$l = strlen($f);
	if ((strpos($f,"'")!== 0) or (strrpos($f,"'")!== $l-1))
		return $f;
	$r = '\''.trim(substr($f, 1, $l-2)).'\'';
	return $r;
}

// une fonction pour le code de |? (la négation de choixsivide)
function choix_selon ($test, $sioui, $sinon) {
	return $test ? $sioui : $sinon;
}


//
// Prépare une expression compilée à être réinjectée dans le code compilé
//
// OK pour une petite variété de formes syntaxiques :
// - Balises sans traitements : #ID_ARTICLE, #TITRE*...
// - #GET{variabledenv}
// À mieux tester et étendre en prenant autrement le pb
//
function reinjecte_expression_compilee($expr_org) {
	$expr = $expr_org;
	// #GET{aa} est implémenté par un appel à table_valeur
	// On traduit en appels de tableau php
	// Pour #GET, le 3eme argument de table_valeur est toujours 'null' donc osef
	// La syntaxe #GET{vartableau/index1/index2} n'est pas gérée
	$expr = preg_replace (
		'/table_valeur\((.*),(.*),.*\)/',   // TODO : affiner pour pouvoir traiter une plus grande variété de codes
		'$1[$2]',
		$expr
	);

	if (($expr!=$expr_org) and isset($_GET['debug']))
		echo "Passe par : <pre style='display:inline'>$expr</pre> ";

	// Variables scalaires $truc et tableaux multiniveaux $Pile[0][$SP]['index']
	$expr = preg_replace(
		'/@?(\$\w+(\[[^\]]*\])*)/',
		'\'{$1}\'',
		$expr);

	if (isset($_GET['debug']))
		echo "et renvoie : <pre>$expr</pre>";
	return $expr;
}

function macrosession_pipe($q="!!! non défini !!!") {
	if (isset($_GET['debug'])) {
		echo "exec macrosession_pipe($q)<br>";
	}
	return $q;
}

/**
 * @param $a
 * @return string
 */
function macrosession_print($a) {
	if (isset($_GET['debug']))
		echo '<pre>'.print_r($a, 1).'</pre>';
	return "''";
}

//
/**
 * @param string $macro         description du contexte d'appel (nom de la macro + arguments éventuellement)
 * @param string $arg_name      nom de l'argument testé
 * @param string $val           code compilé pour l'argument testé et devant être réinjecté
 * @param array $p              pile contexte
 * @param bool $contexte_ok     Désormais et pour l'instant inutilisé
 *                              Indique si les motclé de référence au contexte sont acceptés : env, boucle, url, #GET{variable}, #BALISE
  * @return bool
 */
function erreur_argument_macro($macro, $arg_name, $val, $p, $contexte_ok=false) {
	if (substr($val, 0, 1) != "'") {
		if ($contexte_ok)
			$contexte_ok = "Pour chercher dans les variables d'environnement ou d'url, vous pouvez utiliser 'env', 'boucle', 'url' et aussi '#BALISE' pour les balises reçues par le squelette, mais pas pour les champs de la boucle immédiatement englobante";
		// if (isset($_GET['debug']))
		erreur_squelette ("L'argument '$arg_name' de la macro '$macro' ne doit pas être une valeur calculée (".$val."). $contexte_ok", $p);
		return true;
	};
	return false;
}
