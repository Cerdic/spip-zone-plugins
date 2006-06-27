<?php

function new_widget($id, $classe, $valeur) {
	global $editable;
//error_log("new_widget($id, $classe, $valeur)");
	include_spip('inc/widgets/'.$classe);
	$w= new $classe($id, $valeur);
	$res= $w->code().$w->input();
return $res;
}

//
// genere le code d'un widget donne
// _ syntaxe : <code>#EDITABLE{id,classe,valeur}</code>
// -* id = un identifiant qui doit etre unique dans la page
// -* classe = le type de widget a instancier ('Widget' par defaut)
// -* valeur = le contenu initial du widget (vide par defaut)
//
function balise_EDITABLE($p) {
	global $editable;

//error_log("EDITABLE");
	if ($p->param && !$p->param[0][0] && $p->param[0][1]) {
		$param= $p->param[0];

		// recuperer les params
		$id=  calculer_liste($param[1],
			$p->descr, $p->boucles, $p->id_boucle);
//error_log("id $id");

		if ($param[2]) {
			$classe=  calculer_liste($param[2],
				$p->descr, $p->boucles, $p->id_boucle);
		} else {
			$classe="'Widget'";
		}
//error_log("classe $classe");
		if ($param[3]) {
			$valeur=  calculer_liste($param[3],
				$p->descr, $p->boucles, $p->id_boucle);
		} else {
			$valeur="''";
		}
//error_log("valeur $valeur");
	} else {
		erreur_squelette('EDITABLE necessite au moins 1 parametre');
		return $p;
	}

	$p->code = editable($editable, $id, $classe, $valeur);
	$p->interdire_scripts = false;
	return $p;
}

// genere le code d'un formulaire dans lequel placer des editables
// _ syntaxe : <code>#EDITABLE_DEBUT{actions,url}</code>
// -* actions = un nom de squelette contenant les actions
// -* url = l'url a laquelle revenir apres validation du formulaire
function balise_EDITABLE_DEBUT($p) {
	global $editable;
//error_log("EDITABLE_DEBUT");

	// on force a partir d'un nouveau formulaire
	// => on ne peut en faire qu'un a la fois
	// => verifier si ca met pas la zone dans des <include> ?
	$editable= array('actions' => null);

	if ($p->param && !$p->param[0][0] && $p->param[0][1]) {
		$editable['actions']=  calculer_liste($p->param[0][1],
			$p->descr, $p->boucles, $p->id_boucle);

		if ($p->param[0][2]) {
			$editable['retour']=  calculer_liste($p->param[0][2],
				$p->descr, $p->boucles, $p->id_boucle);
error_log("EDITABLE_DEBUT retour=".$editable['retour']);
			if($editable['retour']=="''") $editable['retour']= 'self()';
		} else {
			$editable['retour']= 'self()';
		}
		if ($p->param[0][3]) {
			$complement=  calculer_liste($p->param[0][3],
				$p->descr, $p->boucles, $p->id_boucle);
error_log("EDITABLE_DEBUT complement=".$complement);
		} else {
			$complement= '';
		}
	} else {
		erreur_squelette('EDITABLE_DEBUT necessite au moins 1 parametre');
		return $p;
	}

	$p->code= editable_debut($editable, $complement);

	return $p;
}

function balise_EDITABLE_FIN($p) {
	global $editable;
//error_log("EDITABLE_FIN");
	$p->code = editable_fin($editable);
	return $p;
}

function editable_debut(&$editable, $complement='') {
	return '"<form method=\'post\' action=\'".self()."\' ".'.$complement.'.">
	<input type=\'hidden\' name=\'action\' value=\'editer\'>
	<input type=\'hidden\' name=\'retour\' value=\'".urlencode('.$editable['retour'].')."\'>
"';
}

function editable_fin(&$editable) {
	return '"<input type=\'hidden\' name=\'actions\' value=\'".($actions='.$editable['actions'].')."\'>
	<input type=\'hidden\' name=\'actions_secu\' value=\'".md5($actions.\' - \'.$GLOBALS[\'meta\'][\'alea_ephemere\'])."\'>
</form>"';
}

function editable(&$editable, $id, $classe, $valeur=null) {
	return "new_widget($id, $classe, $valeur)";
}

?>
