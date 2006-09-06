<?php

function mot_croises_pre_propre($texte){
	include_spip('inc/calculer_grille');
	$tableau = $texte;					//sera uniquement le tableau spip, mais on attend pour le moment
	
	$tableau_php = calcul_tableau_grille2($tableau); 
	include_spip('inc/affichage_grille');
	
	
	return affichage_grille($tableau_php);
}



?>

