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

function extended_session_get ($champ) {
	if ((!defined('_VISITEUR_SESSION_GLOBALE_NON_PRIORITAIRE')
			or !_VISITEUR_SESSION_GLOBALE_NON_PRIORITAIRE)
		and isset($GLOBALS['visiteur_session'][$champ]))
		return $GLOBALS['visiteur_session'][$champ];
	if (function_exists ('_visiteur_session_get'))
		return _visiteur_session_get($champ);
	return null;
};

function balise__VISITEUR_dist($p) {
	$_nom = interprete_argument_balise(1, $p);
	if (!$_nom) {
		$_nom = "'id_auteur'";
	}
	$p->code="'<'.'" . '?php echo extended_session_get("\' . ' . $_nom . ' . \'"); ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}

function balise__VISITEUR_SI_dist($p) {
	$_champ = interprete_argument_balise(1, $p);
	if (!$_champ) {
		$_champ = "'id_auteur'";
	}
	$p->code="'<'.'" . '?php if (extended_session_get("\' . ' . $_champ . ' . \'")) { ?' . "'.'>'";
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
	$p->code="'<'.'" . '?php if (extended_session_get("\' . ' . $_champ . ' . \'") == "\' . ' . $_val . '.\'") { ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}
