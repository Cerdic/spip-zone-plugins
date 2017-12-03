<?php

/**
 * Outils SPIP supplémentaires pour une gestion efficace pour l'hébergement 
 * des accés aux données de la _session courant
 * et pour l'accès à des données de session étendue
 * 
 * Balises #_SESSION, #_SESSION_SI, #_SESSION_SINON, #_SESSION_FIN
 *
 * @copyright	2016, 2017
 * @author 		JLuc
 * @author		Marcimat
 * @licence		GPL
 * 
 */

include_spip('inc/session');
include_spip ('inc/filtres'); 

// on utilise nobreak quand il n'y a pas de break entre 2 cases d'un switch,
// pour témoigner du fait que cette omission est intentionnelle
if (!defined('nobreak'))
	define('nobreak', '');

//
// FIXME : appeler appliquer_filtre dans le code compilé est une somptuosité superfétatoire
// Au lieu de cela, appeler chercher_filtre à la compilation pour savoir quelle est la fonction appelée par le filtre et insérer dans le code compilé un appel direct à cette fonction 
// Comme ça plus besoin d'inclure inc/filtres dans mes_options
//

//
// Accés étendu aux données de session des visiteurs
//
// Aucun test n'est fait en amont sur la présence ou non d'une session :
// le pipeline session_get peut être défini par ailleurs (autre plugin...)
// Il reçoit un tableau avec 2 arguments : 'champ' contient le champ recherché,
// et 'visiteur_session' contient la session en cours d'élaboration,
// qu'il peut, ou non, utiliser pour le calcul ou la recherche de la valeur demandée
//
// Actuellement le pipeline n'est appelé que si la valeur demandée
// n'est pas déjà présente dans la session globale SPIP de base...
// 
function pipelined_session_get ($champ) {
	if (!isset ($GLOBALS['visiteur_session'])
		or !isset($GLOBALS['visiteur_session']['id_auteur'])	// il semble que ces précisions soient nécessaires
		or !$GLOBALS['visiteur_session']['id_auteur'] )
		return '';
	elseif (isset ($GLOBALS['visiteur_session'][$champ]))
		return $GLOBALS['visiteur_session'][$champ];

	$session = 
		array (	'champ' => $champ,
				'visiteur_session' => $GLOBALS['visiteur_session']);
	$session = pipeline ('session_get', $session);
	if (isset ($session['visiteur_session'][$champ]))
		return $session['visiteur_session'][$champ];
	else 
		return '';
};

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

define (V_OUVRE_PHP, "'<'.'" . '?php ');
define (V_FERME_PHP, ' ?' . "'.'>'");

// Appelé uniquement au recalcul pour la compilation
// le code renvoyé sera inséré à l'intérieur d'un '...'
function compile_appel_macro_session ($p) {
	$champ = interprete_argument_balise(1, $p);
	// $champ est entre quotes ''
	if (!$champ)
		$champ = "'id_auteur'";

	if (erreur_argument_macro ('#_SESSION', 'champ', $champ, $p))
		return "''";

	$get_champ = "pipelined_session_get('.\"$champ\".')";

	// champ sans application de filtre
	if (!existe_argument_balise(2, $p)) 
		return $get_champ;

	// Application d'un filtre, récupéré entre quotes ''
	$filtre = trim_quote(interprete_argument_balise (2, $p));
	if (erreur_argument_macro ('#_SESSION', 'filtre', $filtre, $p))
		return "''";

	// le filtre est il en fait un opérateur unaire ?
	if (in_array ($filtre, array ("'!'", "'non'"))) {
		$unaire = trim ($filtre, "'");
		switch ($unaire) {
		case '!':
			nobreak;
		case 'non' :
			return "(!$get_champ)";
			break;
		}
	}

	if ($filtre=="'?'")
		$filtre = "'choix_selon'";
		
	// le filtre peut être appelé avec 0, un ou 2 arguments
	$arg_un = $arg_deux = $virgule_arg_un = $virgule_arg_deux = '';
	
	if (existe_argument_balise(3, $p)) {
		$arg_un = trim_quote(interprete_argument_balise(3, $p));
		if ($arg_un and erreur_argument_macro ('#_SESSION', 'arg_un', $arg_un, $p))
			return "''";
		$virgule_arg_un = ".', '.\"$arg_un\"";
	};

	// le filtre est il en fait un opérateur de comparaison ?
	if (in_array ($filtre, array ("'=='", "'!='", "'<'", "'<='", "'>'", "'>='"))) {
		$comparateur = trim ($filtre, "'");

		return "($get_champ $comparateur '.\"$arg_un\".')";
		// #_SESSION{nom,==,JLuc} donnera 
		// '<'.'?php  echo (pipelined_session_get('."'nom'".') == '."'JLuc'".');  ?'.'>'
	}

	if (existe_argument_balise(4, $p)) {
		$arg_deux = trim_quote(interprete_argument_balise(4, $p));
		if ($arg_deux and erreur_argument_macro ('#_SESSION', 'arg_deux', $arg_deux, $p))
			return "''";
		$virgule_arg_deux = ".', '.\"$arg_deux\"";
	};

// produira par exemple ensuite :
// '<'.'?php  echo appliquer_filtre(pipelined_session_get('."'nom'".'), '."'strlen'".');  ?'.'>'
// ou '<'.'?php  echo appliquer_filtre( pipelined_session_get('."'nbreste'".'), '."'plus'" .', "'3'" .');  ?'.'>'
	$r = "appliquer_filtre($get_champ, '.\"$filtre\" $virgule_arg_un $virgule_arg_deux .')";

	return $r;
}

//
// Définition des balises
// Attention : on ne peut PAS appliquer de filtre sur ces balises ni les utiliser dans une construction conditionnelle [avant(...) après]
// Pour appliquer un filtre, utiliser la syntaxe dédiée avec un argument d'appel de la balise
//

/*
 * #_SESSION rend l'id_auteur si l'internaute est connecté
 * #_SESSION(champ) rend la valeur du champ de session étendue de l'internaute connecté
 * #_SESSION(champ, filtre[, arg1[, arg2]]) applique le filtre au champ de session étendue, avec 0, 1 ou 2 arguments supplémentaires et rend la valeur résultat
 * 
 */
function balise__SESSION_dist($p) {
	$p->code = V_OUVRE_PHP . ' echo '. compile_appel_macro_session($p). '; ' . V_FERME_PHP;
	$p->interdire_scripts = false;
	// echo "On insèrera l'évaluation du code suivant : <pre>".$p->code."</pre>\n\n"; 
	return $p;
}

/*
 * #_SESSION_SI teste si l'internaute est authentifié
 * #_SESSION_SI(champ) teste si le champ de session est non vide
 * #_SESSION_SI(champ, val) teste si le champ de session est égal à val
 * 		C'est un raccourci pour #_SESSION_SI{champ,==,val}
 * #_SESSION_SI(champ, operateur, val) teste si le champ de session se compare positivement à la valeur spécifiée
 * 	selon l'opérateur spécifié, qui peut etre 
 * - soit un comparateur : ==, <, >, >=, <= 
 * - soit un opérateur unaire : ! ou non
 * - soit un filtre (nom de fonction) recevant 2 arguments : la valeur du champ et val. 
 * 		C'est alors le retour qui est testé.
 * Produit par exemple le code suivant :
 * 	'<'.'?php  if (pipelined_session_get('."'nom'".')) {  ?'.'>'
*/
function balise__SESSION_SI_dist($p) {
	// Appelé uniquement au recalcul
	$p->code = V_OUVRE_PHP . 'if ('.compile_appel_macro_session($p, $champ).') { ' . V_FERME_PHP;
	$p->interdire_scripts = false;
	return $p;
}

function balise__SESSION_SINON_dist($p) {
	$p->code = V_OUVRE_PHP.' } else { '.V_FERME_PHP;
	$p->interdire_scripts = false;
	return $p;
}

function balise__SESSION_FIN_dist($p) {
	$p->code = V_OUVRE_PHP.' } '.V_FERME_PHP;
	$p->interdire_scripts = false;
	return $p;
}

function compile_appel_macro_autoriser ($p) {
	$autorisation = interprete_argument_balise(1, $p);

	if (erreur_argument_macro ('#_AUTORISER_SI', 'autorisation', $autorisation, $p))
		return "''";

	// l'autorisation peut être appelé avec 0, un ou 2 arguments
	if (!existe_argument_balise(2, $p)) 
		return "autoriser('.\"$autorisation\".')";

	$type = trim_quote(interprete_argument_balise (2, $p));
	if (erreur_argument_macro ('#_AUTORISER_SI', 'type', $type, $p))
		return "''";

	if (!existe_argument_balise(3, $p)) 
		return "autoriser('.\"$autorisation\".', '.\"$type\".')";

	$id = trim_quote(interprete_argument_balise (3, $p));
	if (erreur_argument_macro ('#_AUTORISER_SI', 'id', $id, $p))
		return "''";

	if (!existe_argument_balise(4, $p)) 
		return "autoriser('.\"$autorisation\".', '.\"$type\".', '.\"$id\".')";

	// Les appels à #_AUTORISER_SI avec arguments $qui et $opt n'ont été testés
	$qui = trim_quote(interprete_argument_balise (4, $p));
	if (erreur_argument_macro ('#_AUTORISER_SI', 'qui', $qui, $p))
		return "''";
	if (!existe_argument_balise(5, $p)) 
		return "autoriser('.\"$autorisation\".', '.\"$type\".', '.\"$id\".')";

	$opt = trim_quote(interprete_argument_balise (5, $p));
	if (erreur_argument_macro ('#_AUTORISER_SI', 'opt', $opt, $p))
		return "''";
	return "autoriser('.\"$autorisation\".', '.\"$type\".', '.\"$id\".', '.\"$opt\".')";
}

function balise__AUTORISER_SI_dist($p) {
	$p->interdire_scripts = false;

	// Appelé uniquement au recalcul
	$p->code = V_OUVRE_PHP . 'if ('.compile_appel_macro_autoriser ($p).') { ' . V_FERME_PHP;
	return $p;
}

function balise__AUTORISER_SINON_dist($p) {
	return balise__SESSION_SINON_dist($p);
}

function balise__AUTORISER_FIN_dist($p) {
	return balise__SESSION_FIN_dist($p);
}
 

function erreur_argument_macro ($macro, $argument, $val, $p) {
	if (substr($val, 0, 1) != "'") {
		erreur_squelette ("L'argument '$argument' de la macro '$macro' ne doit pas être une valeur calculée (".$val.")", $p);
		return true;
	};
	return false;
}
