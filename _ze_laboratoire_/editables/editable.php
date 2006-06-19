<?php

function new_widget($id, $classe, $valeur, $callbacks) {
	global $editable;
error_log("new_widget($id, $classe, $valeur, $callbacks)");
	include_spip('inc/widgets/'.$classe);
	$w= new $classe($id, $valeur);
	$res= $w->code($callbacks).$w->input();
	if($w->callbacks) {
		$editable['callbacks'][$id]= $w->callbacks;
		error_log("$id -> ".$w->callbacks);
	}
	return $res;
}

function doSpipInclude($fond) {
	$contexte_inclus = $GLOBALS['contexte'];
	$contexte_inclus['fond']= $fond;
	ob_start();
	include('ecrire/public.php');
	$inclu= ob_get_clean();
	return $inclu;
}

function compactCallBacks() {
	global $editable;
error_log("compactCallBacks:".var_export($editable['callbacks'], 1));

	$callbacks= '';
	foreach($editable['callbacks'] as $id => $cbs) {
		$callbacks.="&$id=$cbs";
	}
	return $callbacks;
}

//
// genere le code d'un widget donne avec l'action et les callbacks associes
// _ syntaxe : <code>#EDITABLE{id,action,classe,valeur,callbacks}</code>
// -* id = un identifiant qui doit etre unique dans la page
// -* action = le "script" de l'action associee
// -* classe = le type de widget a instancier ('Widget' par defaut)
// -* valeur = le contenu initial du widget (vide par defaut)
// -* callbacks = liste de callbacks (separes par des |) pour valider la saisie
//
function balise_EDITABLE($p) {
	global $editable;

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
		if ($param[4]) {
			$callbacks=  calculer_liste($param[3],
				$p->descr, $p->boucles, $p->id_boucle);
		} else {
			$callbacks="null";
		}
error_log("callbacks $callbacks");
	} else {
		erreur_squelette('EDITABLE necessite au moins 1 parametre');
		return $p;
	}

	$p->code = editable($editable, $id, $classe, $valeur, $callbacks);
	$p->interdire_scripts = false;
	return $p;
}

// genere le code d'un formulaire dans lequel placer des editables
// _ syntaxe : <code>#EDITABLE_DEBUT{actions,url}</code>
// -* actions = un nom de squelette contenant les actions
// -* url = l'url a laquelle revenir apres validation du formulaire
function balise_EDITABLE_DEBUT($p) {
	global $editable;
error_log("EDITABLE_DEBUT");

	// on force a partir d'un nouveau formulaire
	// => on ne peut en faire qu'un a la fois
	// => verifier si ca met pas la zone dans des <include> ?
	$editable= array('callbacks' => array() , 'actions' => null);

	if ($p->param && !$p->param[0][0] && $p->param[0][1]) {
		$actions=  calculer_liste($p->param[0][1],
			$p->descr, $p->boucles, $p->id_boucle);
		$editable['actions']= $actions;

		if ($p->param[0][2]) {
			$editable['retour']=  calculer_liste($p->param[0][2],
				$p->descr, $p->boucles, $p->id_boucle);
		} else {
			$editable['retour']= 'self()';
		}
	} else {
		$editable['actions']='';
	}

	$p->code= editable_debut($editable);

	return $p;
}

function balise_EDITABLE_FIN($p) {
	global $editable;
error_log("EDITABLE_FIN");
	$p->code = editable_fin($editable);
	return $p;
}

function editable_debut(&$editable) {
	return '"<form method=\'post\' name=\'zonesEditables\' action=\'ecrire/index.php\'>
	<input type=\'hidden\' name=\'exec\' value=\'editer\'>
	<input type=\'hidden\' name=\'retour\' value=\'".'.$editable['retour'].'."\'>
"';
}

function editable_fin(&$editable) {
	return '"	<input type=\"submit\" value=\"ok\" />
	<input type=\'hidden\' name=\'actions\' value=\'".(urlencode($actions=doSpipInclude('.$editable['actions'].')))."\'>
	<input type=\'hidden\' name=\'callbacks\' value=\'".urlencode($callbacks=compactCallBacks())."\'>
	<input type=\'hidden\' name=\'actions_secu\' value=\'".md5($actions.\' - \'.$GLOBALS[\'meta\'][\'alea_ephemere\'].\' - \'.$callbacks)."\'>
</form>"';
}

function editable(&$editable, $id, $classe, $valeur=null, $callbacks=null) {
	return "new_widget($id, $classe, $valeur, $callbacks)";
}

?>
