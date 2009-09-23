<?php

function inclusdans ($texte, $param) {
	
	$param_array = explode(",", $param);
	foreach($param_array as $value) {
		if (intval(trim($value)) == intval(trim($texte)))
			return true;
	}

	
	return false;
}
function contient($texte, $findme) {
	return (strpos($texte, $findme) !== false);
}

function memoriser_resultat_jeu($texte, $id_jeu) {


		$tab_jeux_reussis = split(",", $_COOKIE['jeux_reussis']);
		if (!in_array($id_jeu, $tab_jeux_reussis)) {
			return "Resultat m&eacute;moris&eacute;";
		}
	return "Vous avez d&eacute;j&agrave; r&eacute;ussi ce jeu";
}
function remplir_sacoche($texte, $id_objet, $img_src) {

	//mise � jour de la liste des objets ramass�s
	
	$objet_ajoute = "<li><div class='objet".$id_objet."'><img src='".$img_src."' /><span style='display: none;'>".$id_objet."</span></div></li>";
	if ($_COOKIE['objets_ramasses']) setcookie("objets_ramasses", $_COOKIE['objets_ramasses'].$objet_ajoute);
	else setcookie("objets_ramasses", $objet_ajoute);
	return " - Un objet a &eacute;t&eacute; donn&eacute; en r&eacute;compense <br /><ul>".$_COOKIE['objets_ramasses']."</ul>";
}

?>