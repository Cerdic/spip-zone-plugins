<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

function rezosocios_nom($nom){
	include_spip('inc/rezosocios');
	
	$rezosocios = rezosocios_liste();
	
	if(isset($rezosocios[$nom]))
		$nom = $rezosocios[$nom]['nom'];
	
	return $nom;
}

function rezosocios_url($nom,$compte){
	include_spip('inc/rezosocios');
	
	$rezosocios = rezosocios_liste();
	
	if(isset($rezosocios[$nom]))
		$url = $rezosocios[$nom]['url'].$compte;
	else
		$url = false;
	
	return $url;
}

function rezosocios_logo($nom){
	$logo = chemin_image($nom.'-32.png');
	return $logo;
}
?>
