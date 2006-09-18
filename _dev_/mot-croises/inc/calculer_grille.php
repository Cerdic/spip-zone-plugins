<?php
function calcul_tableau_grille($texte){
	$texte = trim($texte);
	
	$tableau = explode("\r",$texte);	

	$j =0;
	
	foreach ($tableau as $i){	//ligne par ligne
		
		$tableau[$j] = explode('|',trim($i));		//une cellule, c'est beau !
		array_shift($tableau[$j]);
		array_pop($tableau[$j]);
		$j++;
		
		}
	
	return $tableau;}


?>
