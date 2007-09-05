<?php

/**
 * permet d'appeler un code php qui effectue des traitements avant
 * l'interprétation du squelette.
 * TRAITEMENT{toto} va chercher un fichier toto.php et appeler une fonction
 * traitement_toto ou celle spécifiée comme second argument de la balise.
 * la fonction est appelée avec un tableau en argument, correspondant à l'ENV,
 * qu'on peut donc modifier pour agir sur l'affichage du squelette ensuite.
 */
function balise_TRAITEMENT($p) {
	$fi= interprete_argument_balise(1,$p);
	if(!($fc = interprete_argument_balise(2,$p))) {
		$fc= 'null';
	}

	$p->code = "((\$f=appelerTraitement($fi, $fc))&&\$f(\$Pile[0]))";
	return $p;
}

function barre_etat($etat, $id) {
	switch($etat) {
	case 'new':
	case 'mod':
		return etatRouge($id).etatToOrange($id).etatToVert($id);
		break;
	case 'trv':
		return etatOrange($id).etatToVert($id);
		break;
	case 'ok':
		return etatVert($id).etatToOrange($id);
		break;
	case 'sup':
		break;
	}
}

function etatRouge($id) {
	return "<img id='rouge:$id' src='".find_in_path('rouge.png')."'/>";
}
function etatOrange($id) {
	return "<img id='orange:$id' src='".find_in_path('orange.png')."'/>";
}
function etatVert($id) {
	return "<img id='vert:$id' src='".find_in_path('vert.png')."'/>";
}
function etatToOrange($id) {
	return "<img onclick='$(\"#etat_$id\").load(\"?action=docjquery&value=trv&id=etat:$id\")' id='orange:$id' src='".find_in_path('toOrange.png')."'/>";
}
function etatToVert($id) {
	return "<img onclick='$(\"#etat_$id\").load(\"?action=docjquery&value=ok&id=etat:$id\")' id='vert:$id'src='".find_in_path('toVert.png')."'/>";
}
?>
