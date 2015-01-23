<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

function rezosocios_nom($nom){
	include_spip('inc/rezosocios');
	
	$noms = rezosocios_liste();
	
	if(isset($noms[$nom]))
		$nom = $noms[$nom];
	
	return $nom;
}
?>
