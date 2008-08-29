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

	//mise à jour de la liste des lieux visités
		$tab_jeux_reussis = split(",", $_COOKIE['jeux_reussis']);
		if (!in_array($id_jeu, $tab_jeux_reussis)) {
			setcookie("jeux_reussis",implode(",", $tab_jeux_reussis).",".$id_jeu);
		}
	return "Resultat m&eacute;moris&eacute;";
}

?>