<?php

function critere_IN($idb, &$boucles, $crit) {
	static $cpt = 0;

	$p= &$crit->param[1][0];
	if($p->type != 'texte' || $p->texte{0}!='*') {
		// C'est pas le cas qui nous interesse
		return critere_IN_dist($idb, &$boucles, $crit);
	}

	// on enleve le marqueur
	$p->texte= preg_replace('/^\*\s*/', '', $p->texte);
	if(strlen($p->texte)==0) {
		array_shift($crit->param[1]);
	}

	// et on enchaine
	if ($crit->not) {
		$crit->op="NOT IN";
		$crit->not= false;
	}

	list($arg, $op, $val, $col)= calculer_critere_infixe($idb, $boucles, $crit);

	// bout de code permettant de generer la liste dans le "in"
	$var = '$inlight' . $cpt++;
	$x= "\n\t$var = array();";
	foreach ($val as $k => $v) {
		if (preg_match(",^(\n//.*\n)?'(.*)'$,", $v, $r)) {
		  // optimiser le traitement des constantes
			if (is_numeric($r[2]))
				$x .= "\n\t$var" . "[]= $r[2];";
			else
				$x .= "\n\t$var" . "[]= " . _q($r[2]) . ";";
		} else {
		  // Pour permettre de passer des tableaux de valeurs
		  // on repere l'utilisation brute de #ENV**{X}, 
		  // c'est-a-dire sa  traduction en ($PILE[0][X]).
		  // et on deballe mais en rajoutant l'anti XSS
		  $x .= "\n\tif (!(is_array(\$a = ($v))))\n\t\t$var" ."[]= \$a;\n\telse $var = array_merge($var, \$a);";
		}
	}
	$boucles[$idb]->in .= $x;

	error_log("==> $arg, $op, ".var_export($val, 1).", $col");

	$where = array("'$op'", "'$arg'", "'('.join(',',array_map('_q', $var)).')'");

	error_log("=> where = ".var_export($where, 1));

	$boucles[$idb]->where[]= (!$crit->cond ? $where :
	  array("'?'",
		calculer_argument_precedent($idb, $col, $boucles),
		$where,
		"''"));
}

?>
