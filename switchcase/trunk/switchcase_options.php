<?php

/**
 * Balises SPIP génériques supplémentaires, du genre Bonux
 *
 * @copyright  2015-2016
 * @author     JLuc chez no-log.org
 * @licence    GPL
 */

function balise_SWITCH_dist($p) {
	$_val = interprete_argument_balise(1, $p);
	if ($_val === NULL) {
		$err = array('zbug_balise_sans_argument', array('balise' => ' #SWITCH'));
		erreur_squelette($err, $p);
	}
	else
		$p->code = $p->code = "(vide(\$Pile['vars']['_switch_'] = $_val).vide(\$Pile['vars']['_switch_matched_']=''))";
		// #GET{_switch_} renvoie maintenant la valeur testée
		// et #GET{_switch_matched_} indique si un test #CASE a déjà été satisfait

	$p->interdire_script = false;
	return $p;
}

function balise_CASE_dist($p) {
	$tested = interprete_argument_balise(1, $p);
	if ($tested === NULL) {
		$err = array('zbug_balise_sans_argument', array('balise' => ' #CASE'));
		erreur_squelette($err, $p);
	}
	else {
		$p->code = "(($tested == \$Pile['vars']['_switch_']) ? ' '.vide(\$Pile['vars']['_switch_matched_']=' ') : '')";
	}; 
	$p->interdire_script = false;
	return $p;
}

function balise_CASE_DEFAULT_dist($p) {
	$p->code = "(\$Pile['vars']['_switch_matched_'] ? '' : ' ')";
	$p->interdire_script = false;
	return $p;
}
