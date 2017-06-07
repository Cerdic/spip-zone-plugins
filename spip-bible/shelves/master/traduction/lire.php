<?php
include_spip('inc/bible_tableau');
function generer_url_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang){
	list($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit) = lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
}

function lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	//petit livre ?
	$petit_livre=bible_tableau('petit_livre',$lang);
	if (in_array(strtolower($livre),$petit_livre)) {
		
		$verset_debut=$chapitre_debut;
		
		$verset_fin = $chapitre_fin;
		$chapitre_debut = 1;
		$chapitre_fin = 1;
		$petit		= true;
	
	} 
	return array($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit);

}
function recuperer_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang){
	$param_cache = array(	'version'=>4,
				'livre'=>$livre,
				'chapitre_debut'=>$chapitre_debut,
				'verset_debut'=>$verset_debut,
				'chapitre_fin'=>$chapitre_fin,
				'verset_fin'=>$verset_fin,
				'lire'=>$lire);
	
	//Vérifions qu'on a pas en cache
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}
	list($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit) = lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	

	$tableau_resulat = array();
	
	//recuperation du passage
	include_spip("inc/distant");
	include_spip("inc/charsets");
	
	
	if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$tableau_resultat);
	}
	return $tableau_resultat;
}
