<?php

/**
 * Outils SPIP supplémentaires pour une gestion efficace pour l'hébergement des accés aux données du visiteur courant
 * Balises #_VISITEUR_SI, #_VISITEUR_SI_EGAL, #_VISITEUR_SINON, #_VISITEUR_FINSI.
 *
 * @copyright	2016
 * @author		Marcimat
 * @author 		JLuc
 * @licence		GPL
 * 
 */

include_spip('inc/session');

function balise__VISITEUR_SI_dist($p) {
	$_champ = interprete_argument_balise(1, $p);
	if (!$_champ) {
		$_champ = "'id_auteur'";
	}
	$p->code="'<'.'" . '?php if (session_get("\' . ' . $_champ . ' . \'")) { ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}
 
function balise__VISITEUR_FINSI_dist($p) {
	$p->code="'<'.'" . '?php }; ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}
 
function balise__VISITEUR_SINON_dist($p) {
	$p->code="'<'.'" . '?php } else { ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}
 
//
// il faudrait aussi < et <=, preg_match et d'autres 
// donc cette balise inadaptée est OBSOLÈTE dès sa première publication
//
function balise__VISITEUR_SI_EGAL_dist($p) {

	$_champ = interprete_argument_balise(1, $p);
	$_val = interprete_argument_balise(2, $p);
	if (!$_champ) {
		$_champ = "'id_auteur'";
	}
	$p->code="'<'.'" . '?php if (session_get("\' . ' . $_champ . ' . \'") == "\' . ' . $_val . '.\'") { ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}
