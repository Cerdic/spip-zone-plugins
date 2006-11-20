<?php
#---- filtres mot-croisés ----------#
#Filtre : grille de mot croisés		#
#Auteur : Maïeul Rouquette,2006		#
#Licence : GPL				#
#Contact : maieulrouquette@tele2.fr	#

define(_QCM_ECHAP, '<!-- grille -->');

//evite d'afficher la grille la ou on veux pas (par exemple pour les backend)
function pas_de_grille ($texte){

		include_spip('inc/affichage_grille');
		$j = 0;
		$texte = preg_split ('/'._QCM_ECHAP.'/',$texte);
		
		foreach ($texte as $i){
			
			if ($j%2==1){
				$texte[$j]='';
				}
			$j++;
			}
		
	return implode($texte);
}




function grille($texte,$page=''){						//on garde  pour compatibilité
	return $texte;
	
	    } 
#--- fin filtre mot-croisés ---#

?>