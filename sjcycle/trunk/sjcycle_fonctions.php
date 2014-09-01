<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function randomString($length = 8){
	$passe = "";
	$consonnes = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "z", "bl", "br", "cl", "cr", "ch", "dr", "fl", "fr", "gl", "gr", "pl", "pr", "qu", "sl", "sr");
	$voyelles = array("a", "e", "i", "o", "u", "ae", "ai", "au", "eu", "ia", "io", "iu", "oa", "oi", "ou", "ua", "ue", "ui");

	$nbrC = count($consonnes) - 1;
	$nbrV = count($voyelles) - 1;

	for ($i = 0; $i < $length; $i++){
		$passe .= $consonnes[rand(0, $nbrC)] . $voyelles[rand(0, $nbrV)];
	}
	return substr($passe, 0, $length);
}