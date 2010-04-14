<?php

// Pour faire des heures plus jolies
function forum_urbagora_supprimer_le_zero($heure) {
if($heure=='01') $heure = '1';
if($heure=='02') $heure = '2';
if($heure=='03') $heure = '3';
if($heure=='04') $heure = '4';
if($heure=='05') $heure = '5';
if($heure=='06') $heure = '6';
if($heure=='07') $heure = '7';
if($heure=='08') $heure = '8';
if($heure=='09') $heure = '9';
return $heure; 
}


// Pluriel (très rudimentaire) d'un mot
function forum_urbagora_pluriel ($nb) {
	if($nb>1) return "s";
	else return "";
}

function urbagora_forum_prenom_nom($nom) {
	if(function_exists(prenom_nom))
		return prenom_nom($nom);
	else
		return $nom;

}


?>
