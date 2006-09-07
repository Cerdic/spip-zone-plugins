<?php
#---- filtres mot-croisés ----------#
#Filtre : grille de mot croisés		#
#Auteur : Maïeul Rouquette,2006		#
#Licence : GPL				#
#Contact : maieulrouquette@tele2.fr	#



function pas_de_grille ($texte){
	//evite d'afficher la grille la ou on veux pas (par exemple pour les backend)
		$texte = preg_replace ('/<(grille)>(.*)<\\1>,UimsS/','',$texte);
		//$texte = ereg_replace ('<p class="spip"><grille></p>.*</grille></p>','',$texte);
		/*A tester
		$texte = preg_replace(',<(grille)>(.*)<\/\1>,UimsS','',$texte);
		*/
	return $texte;
}



function grille($texte,$page=''){						//on garde  pour compatibilité
	return $texte;
	
	    } 
#--- fin filtre mot-croisés ---#

?>