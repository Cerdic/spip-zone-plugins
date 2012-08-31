<?php

function generer_url_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	// passage en int
	settype($chapitre_debut,'int');
	settype($chapitre_fin,'int');
	settype($verset_fin,'int');
	settype($verset_debut,'int');
	// Correction pour correspondre au systeme de NA
	if ($verset_fin == 0){
		$verset_fin= 9999;	
	}
	if ($verset_debut == 0){
		$verset_debut=1;
	}
	
	$livres = 	bible_tableau('gateway');
	$base 	= 'http://www.nestle-aland.com/en/online-lesen/text/bibeltext/lesen/stelle';
	$id		= ($livres[$lang][$livre]) -46; // numéro du livre
	
	$debut  = $chapitre_debut * 10000 + $verset_debut;
	$fin	= $chapitre_fin * 10000 + $verset_fin;
	return ($base."/$id/$debut/$fin");
}
function recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){

	return generer_url_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);	
}
?>
