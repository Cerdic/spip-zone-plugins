<?php

/**
 * Balises SPIP génériques supplémentaires, du genre Bonux
 *
 * @copyright  2015
 * @author     JLuc chez no-log.org
 * @licence    GPL
 */

function balise_SWITCH_dist($p) {
	$_val = interprete_argument_balise(1, $p);
	if ($_val === NULL)
		$p->code="'il faut 1 argument pour la balise #SWITCH et très exactement 1 pour l\'instant'";
	else
		$p->code = $p->code = "vide(\$Pile['vars'][\$_zzz=(string)'_switch_'] = $_val)";
		// #GET{_switch_} vaut maintenant la valeur testée

	$p->interdire_script = false;
	return $p;
}



function balise_CASE_dist($p) {
	$tested = interprete_argument_balise(1, $p);
	if ($tested === NULL)
		$p->code="'il faut 1 ou 2 arguments pour la balise #CASE (1 seulement si on a fait # SWITCH(XXX) avant : todo)'";
	else {
		$value = interprete_argument_balise(2, $p);
		if ($value === NULL) {
			$value=$tested;
			$tested = "(\$Pile['vars'][\$_zzz=(string)'_switch_'])";
		}
		$p->code = "(($tested == $value) ? ' ' : '')";
	};
	$p->interdire_script = false;
	return $p;
}

