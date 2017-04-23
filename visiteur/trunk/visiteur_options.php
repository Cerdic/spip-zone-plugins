<?php

/**
 * Outils SPIP supplémentaires pour une gestion efficace pour l'hébergement 
 * des accés aux données du visiteur courant
 * Balises #_VISITEUR, #_VISITEUR_SI, #_VISITEUR_SINON, #_VISITEUR_FIN
 *
 * @copyright	2016, 2017
 * @author 		JLuc
 * @author		Marcimat
 * @licence		GPL
 * 
 */

include_spip('inc/session');
include_spip ('inc/filtres'); 
// appeler appliquer_filtre dans le code compilé est une somptuosité superfétatoire
// todo : à la compilation appeler chercher_filtre pour savoir quelle est la fonction appelée par le filtre et insérer dans le code compilé un appel direct à cette fonction --> plus besoin d'inclure inc/filtres dans mes_options

//
// Accés étendu aux données des visiteurs
//
// Aucun test n'est fait en amont sur la présence ou non d'une session :
// la fonction _visiteur_session_get définie par ailleurs (autre plugin...)
// doit gérer cette éventualité et renvoyer null ou une valeur par défaut
//
// todo : un pipeline à la spip
//
function extended_session_get ($champ) {
	if ((!defined('_VISITEUR_SESSION_GLOBALE_NON_PRIORITAIRE') // todo revoir sémantique et nomenklatura de ces constantes
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
// $champ est entre quotes ''
// le code renvoyé sera inséré à l'intérieur d'un '...'
function compile_appel_visiteur ($p, $champ,$n=2) {
	$get_champ = "extended_session_get('.\"$champ\".')";
	
	// champ sans application de filtre
	if (!existe_argument_balise($n, $p)) 
		return $get_champ;
	
	// Application d'un filtre, récupéré entre quotes ''
	$filtre = trim_quote(interprete_argument_balise (2, $p));

	if ($filtre=="'?'")
		$filtre = "'choix_selon'";
		
	// le filtre peut être appelé avec 0, un ou 2 arguments
	$arg_un = $arg_deux = $virgule_arg_un = $virgule_arg_deux = '';
	
	if (existe_argument_balise($n+1, $p)) {
		$arg_un = interprete_argument_balise($n+1, $p); 
		$virgule_arg_un = ".', '.\"$arg_un\"";
	};

	// le filtre est il en fait un opérateur de comparaison ?
	if (in_array ($filtre, array ("'=='", "'!='", "'<'", "'<='", "'>'", "'>='"))) {
		$comparateur = trim ($filtre, "'");
										
		$r = "($get_champ $comparateur '.\"$arg_un\".')";
		// #_VISITEUR{nom,==,JLuc} donnera 
		// '<'.'?php  echo (extended_session_get('."'nom'".') == '."'JLuc'".');  ?'.'>'
		// #_VISITEUR_SI{nom
		return $r;
	}
	
	if (existe_argument_balise($n+2, $p)) {
		$arg_deux = interprete_argument_balise($n+2, $p);
		$virgule_arg_deux = ".', '.\"$arg_deux\"";
	};

// produira par exemple ensuite :
// '<'.'?php  echo appliquer_filtre(extended_session_get('."'nom'".'), '."'strlen'".');  ?'.'>'
// ou '<'.'?php  echo appliquer_filtre( extended_session_get('."'nbreste'".'), '."'plus'" .', "'3'" .');  ?'.'>'
	$r = "appliquer_filtre($get_champ, '.\"$filtre\" $virgule_arg_un $virgule_arg_deux .')";

	return $r;
}

//
// Définition des balises
// Attention : on ne peut PAS appliquer de filtre sur ces balises ni les utiliser dans une construction conditionnelle [avant(...) après]
// Pour appliquer un filtre, utiliser la syntaxe dédiée avec un argument d'appel de la balise
//

/*
 * #_VISITEUR rend l'id_auteur si l'internaute est connecté
 * #_VISITEUR(champ) rend la valeur du champ de session étendue de l'internaute connecté
 * #_VISITEUR(champ, filtre[, arg1[, arg2]]) applique le filtre au champ de session étendue, avec 0, 1 ou 2 arguments supplémentaires et rend la valeur résultat
 */
function balise__VISITEUR_dist($p) {
	$champ = interprete_argument_balise(1, $p);
	if (!$champ)
		$champ = "'id_auteur'";
	$p->code = V_OUVRE_PHP . ' echo '. compile_appel_visiteur($p, $champ). '; ' . V_FERME_PHP;
	$p->interdire_scripts = false;
	// echo "On insèrera l'évaluation du code suivant : <pre>".$p->code."</pre>\n\n"; 
	return $p;
}

/*
 * #_VISITEUR_SI(champ) teste si le champ de session est non vide
 * #_VISITEUR_SI(champ, val) teste si le champ de session est égal à la valeur spécifiée
 * #_VISITEUR_SI(champ, val, operateur) teste si le champ de session se compare positivement à la valeur spécifiée
 * 	selon l'opérateur spécifié, qui peut etre 
 * - soit un comparateur : ==, <, >, >=, <= 
 * - soit un filtre (nom de fonction) recevant 2 arguments : la valeur du champ et val. C'est le retour qui est alors testé.
 * Produit par exemple le code suivant :
 * '<'.'?php  echo extended_session_get('."'nom'".');  ?'.'>'
*/
function balise__VISITEUR_SI_dist($p) {
	$champ = interprete_argument_balise(1, $p);
	if (!$champ)
		$champ = "'id_auteur'";

	$p->interdire_scripts = false;

	// Appelé uniquement au recalcul
	$p->code = V_OUVRE_PHP . 'if ('.compile_appel_visiteur($p, $champ).') { ' . V_FERME_PHP;
	// echo "On insèrera l'évaluation du code suivant : <pre>".$p->code."</pre>\n\n";
	return $p;
}

function balise__VISITEUR_SINON_dist($p) {
	$p->code="'<'.'" . '?php } else { ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}

function balise__VISITEUR_FIN_dist($p) {
	$p->code="'<'.'" . '?php }; ?' . "'.'>'";
	$p->interdire_scripts = false;
	return $p;
}
 
