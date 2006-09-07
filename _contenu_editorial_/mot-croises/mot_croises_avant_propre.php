<?php

function mot_croises_pre_propre($texte){
	include_spip('inc/calculer_grille');
	
	$tableau = preg_split("/<grille>|<\/grille>/",$texte);					//sera uniquement le tableau spip, mais on attend pour le moment
	$j =0;
	foreach ($tableau as $i){
			if ($j!=0 and $j!=count($tableau)-1)	//pas lex extremités du tableau
				{
				include_spip('inc/affichage_grille');
				$tableau[$j] = affichage_grille(calcul_tableau_grille2($tableau[$j]));
				
				}
			
			
			$j++;
			}
		
		
	
	
	
	include_spip('inc/affichage_grille');
	
	
	return implode($tableau);
}



?>