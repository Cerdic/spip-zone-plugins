<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
		if (debug_get_mode('macrosession')) {
			echo "_SESSION_SI\{$champ $filtre $arg_un}<br>";
		}

		if (strpos($arg_un,"'") !== 0) {
			// Exemple : @$Pile[0]['id_auteur'] pour #ID_AUTEUR
			// table_valeur($Pile["vars"], (string)'debut', null) pour #GET{debut}
			$arg_un = reinjecte_expression_compilee($arg_un);
		}
		// FIXME : erreur_argument_macro est insuffisant pour anticiper ici les erreurs de syntaxe php

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
		if (strpos($arg_deux,"'") !== 0) {
			$arg_deux = reinjecte_expression_compilee($arg_deux);
		}
		$virgule_arg_deux = ".', '.\"$arg_deux\"";
	};

// produira par exemple ensuite :
// '<'.'?php  echo appliquer_filtre(pipelined_session_get('."'nom'".'), '."'strlen'".');  ?'.'>'
// ou '<'.'?php  echo appliquer_filtre( pipelined_session_get('."'nbreste'".'), '."'plus'" .', "'3'" .');  ?'.'>'
	$r = "appliquer_filtre($get_champ, '.\"$filtre\" $virgule_arg_un $virgule_arg_deux .')";

	if (debug_get_mode('macrosession'))
		echo "<b>compile_appel_macro_session renvoie :</b><pre>$r</pre>";
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
	$p->code = V_OUVRE_PHP . 'if ('.compile_appel_macro_session($p).') { ' . V_FERME_PHP;
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
