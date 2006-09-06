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



function grille($texte,$page=''){						//on garde $page pour compatibilité
	if (eregi("<grille>",$texte)!=1){return $texte;} //verifie s'il y a une grille de mot croisé
    
    include_spip('inc/affichage_grille');
    include_spip('inc/calculer_grille');
    
	$tableau_grille=calcul_tableau_grille($texte);
    
   
    
	$grille_formulaire=affichage_grille($tableau_grille);
	
	if ($GLOBALS["bouton_envoi"] == ''){$erreur='';}
	else {
		include_spip('inc/verification');
		
    		$nbr_erreur=comparaison($tableau_grille); 
    		
		$erreur='<strong class="erreur">';
		if ($nbr_erreur==0){$erreur.= _T('grille:aucune_erreur');}
    		if ($nbr_erreur>=2){$erreur.= _T("grille:nombre_erreurs", Array('nbr'=>$nbr_erreur));}
    		if ($nbr_erreur==1){$erreur.= _T('grille:1erreur');}
		$erreur.='</strong>';
		}
       
                              
	$texte=eregi_replace('<p class="spip"><grille></p>.*</grille></p>',$erreur.$grille_formulaire,$texte);
    // les defs verticals
    $texte=eregi_replace('<p class="spip"><vertical></p>','<div class="spip vertical"><h4 class="spip grille">'._T('grille:verticalement').' :</h4>',$texte);
   	$texte=eregi_replace("</vertical></li></ol>",'</li></ol></div>',$texte);
    
    // les definitions horizontal
    $texte=eregi_replace('<horizontal></p>','<div class="spip horizontal"><h4 class="spip grille">'._T('grille:horizontalement').' :</h4>',$texte);
    $texte=eregi_replace("</horizontal></li></ol>",'</li></ol></div>',$texte);
   
   	if ($GLOBALS["solution"][0] == 1){$texte = $texte.affichage_grille($tableau_grille,true);}
     
    
    return $texte;
    } 
#--- fin filtre mot-croisés ---#

?>