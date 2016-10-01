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

//
// accés étendu aux données des visiteurs
//
function extended_session_get ($champ) {
	if ((!defined('_VISITEUR_SESSION_GLOBALE_NON_PRIORITAIRE')
			or !_VISITEUR_SESSION_GLOBALE_NON_PRIORITAIRE)
		and isset($GLOBALS['visiteur_session'][$champ]))
		return $GLOBALS['visiteur_session'][$champ];
	if (function_exists ('_visiteur_session_get'))
		return _visiteur_session_get($champ);
	return null;
};

if (!function_exists('existe_argument_balise')) {
	// prolégomène à interprete_argument_balise
	function existe_argument_balise ($n, $p) {
		return (($p->param) && (!$p->param[0][0]) && (count($p->param[0])>$n));
	};
}

// une fonction pour le code de |? 
// (n'existe t elle pas déjà ? c'est l'inverse de choixsivide)
function siouisinon ($test, $sioui, $sinon) {
	return $test ? $sioui : $sinon;
}

define (V_OUVRE_PHP, "'<'.'" . '?php ');
define (V_FERME_PHP, ' ?' . "'.'>'");
define (V_VIRGULE_ARGUMENT, ' .\'","\'. ');

function compile_appel_visiteur ($p, $champ) {
	$r = 'extended_session_get("\' . ' . $champ . ' . \'")';

	// S'il y a un filtre
	if (existe_argument_balise(2, $p)) {
		$filtre = interprete_argument_balise (2, $p);
		if ($filtre=="'?'")
			$filtre = "'siouisinon'";

		// le filtre peut être appelé avec 0, un ou 2 arguments
		$arg_gauche = $arg_droite = '';

		if (existe_argument_balise(3, $p))
			$arg_gauche = V_VIRGULE_ARGUMENT . interprete_argument_balise(3, $p);

		if (existe_argument_balise(4, $p))
			$arg_droite = V_VIRGULE_ARGUMENT . interprete_argument_balise(4, $p);
			
		$r = 'appliquer_filtre(extended_session_get("\' . ' . $champ . ' . \'"),"\'. '. $filtre . $arg_gauche . $arg_droite . ' .\'")';
	}
	return $r;
}

function balise__VISITEUR_dist($p) {
	$champ = interprete_argument_balise(1, $p);
	if (!$champ)
		$champ = "'id_auteur'";
	$p->code = V_OUVRE_PHP . ' echo '. compile_appel_visiteur($p, $champ). '; ' . V_FERME_PHP;
	$p->interdire_scripts = false;
	return $p;
}

function balise__VISITEUR_SI_dist($p) {
	$champ = interprete_argument_balise(1, $p);
	if (!$champ)
		$champ = "'id_auteur'";
	$p->code = V_OUVRE_PHP . 'if ('. compile_appel_visiteur($p, $champ). ') { ' . V_FERME_PHP;
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

	$champ = interprete_argument_balise(1, $p);
	$_val = interprete_argument_balise(2, $p);
	if (!$champ) {
		$champ = "'id_auteur'";
	}
	$p->code="'<'.'" . '?php if (extended_session_get("\' . ' . $champ . ' . \'") == "\' . ' . $_val . '.\'") { ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}
