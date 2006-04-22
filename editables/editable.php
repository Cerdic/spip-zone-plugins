<?php

function new_widget($id, $classe, $valeur) {
error_log("new_widget($id, $classe, $valeur)");
	include_spip('inc/widgets/'.$classe);
	$w= new $classe($id, $valeur);
	return $w->code().$w->input();
}

function doSpipInclude($fond) {
	$contexte_inclus = array('fond' => $fond);
	ob_start();
	include('ecrire/public.php');
	$inclu= ob_get_clean();
	return $inclu;
}

//
// génère le code d'un widget donné avec l'action et les callbacks associés
// _ syntaxe : <code>#EDITABLE{id,action,classe,valeur,callbacks}</code>
// -* id = un identifiant qui doit être unique dans la page
// -* action = le "script" de l'action associée
// -* classe = le type de widget à instancier ('Widget' par défaut)
// -* valeur = le contenu initial du widget (vide par defaut)
// -* callbacks = liste de callbacks (séparés par des |) pour valider la saisie
//
function balise_EDITABLE($p) {
error_log("EDITABLE");
	if ($p->param && !$p->param[0][0] && $p->param[0][1]) {
		$param= $p->param[0];

		// recuperer les params
		$id=  calculer_liste($param[1],
			$p->descr, $p->boucles, $p->id_boucle);
error_log("id $id");

		if ($param[2]) {
			$classe=  calculer_liste($param[2],
				$p->descr, $p->boucles, $p->id_boucle);
		} else {
			$classe="'Widget'";
		}
error_log("classe $classe");
		if ($param[3]) {
			$valeur=  calculer_liste($param[3],
				$p->descr, $p->boucles, $p->id_boucle);
		} else {
			$valeur="''";
		}
error_log("valeur $valeur");
	} else {
		erreur_squelette('EDITABLE necessite au moins 1 parametre');
		return $p;
	}

	$p->code = "new_widget($id, $classe, $valeur)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_EDITABLE_DEBUT($p) {
error_log("EDITABLE_DEBUT");
	if ($p->param && !$p->param[0][0] && $p->param[0][1]) {
		$actions=  calculer_liste($p->param[0][1],
			$p->descr, $p->boucles, $p->id_boucle);
		$actions= '(urlencode($actions=doSpipInclude('.$actions.')))';
	} else {
		$actions='';
	}

	$p->code= '"<form method=\'post\' name=\'zonesEditables\' action=\'ecrire/index.php\'>
	<input type=\'hidden\' name=\'exec\' value=\'editer\'>
	<input type=\'hidden\' name=\'retour\' value=\'".self()."\'>
	<input type=\'hidden\' name=\'actions\' value=\'".'.$actions.'."\'>
	<input type=\'hidden\' name=\'actions_secu\' value=\'".md5($GLOBALS[\'meta\'][\'alea_ephemere\'].\' - \'.$actions)."\'>
"';

	return $p;
}

function balise_EDITABLE_FIN($p) {
	$p->code = '"	<input type=\"submit\" value=\"ok\" />
</form>"';
	return $p;
}

?>
