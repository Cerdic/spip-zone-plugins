<?php

define(_GRILLE_, '<!-- grille -->');

//retourne la lettre correpondant au chiffre
function lettre_grille($texte) {
	$alphabet="*ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	return $alphabet[$texte];
}

function affichage_grille($tableau_grille, $solution=false){
	//affiche la grille de mot croisés, avec la solution au cas où
	
	// les variables de la grille
	(! $solution) ? $page=self() : pass ; 
	$hauteur =sizeof($tableau_grille);
    $largeur= sizeof($tableau_grille[0]);
    $grille='';
    //fin variable de la grille
    
    (! $solution) ? $grille.="<form class=\"grille\" action=\"".$page."\" method=\"post\">\n" : $grille.="<div class=\"solution\"><h2 class=\"spip\">"._T('motscroises:Solution')." : </h2>" ;	// debut formulaire
    
    $grille.='<table class="grille" cellspacing="0" border="0" summary="'._T('motscroises:table_summary',Array('hauteur'=>$hauteur,'largeur'=>$largeur))."\">\n
    \t<tr>\n\t\t<td class=\"coin\"></td>\n";	// debut tableau + 1ere celule
	
	$increment_largeur=1;   //un iccrément pour les cellules d'entete
	
	//les cellules d'entetes verticales
	while ($increment_largeur<=$largeur){
        $grille.="\t\t<th scope=\"col\">".$increment_largeur."</th>\n";
        $increment_largeur++;}
	// fin des cellules d'entête verticale
	
	$grille=$grille."\t</tr>\n";		//cloture de la ligne d'entête
	
	//debut affichage des lignes
	foreach($tableau_grille as $ligne =>$contenu_ligne){
		$ligne++;
		$grille=$grille."\t<tr>\n\t<th scope=\"row\">".lettre_grille($ligne)."</th>\n";	//numeros de ligne
		
		foreach ($contenu_ligne as $colonne =>$cellule){
		    $colonne++;
		    //s'il s'agit d'un noir
		    if ($cellule == "*") 
		    	$grille .= "\t\t<td class=\"noir\">*</td>\n";
				else if ($solution)
					$grille .= "\t\t<td>$cellule</td>\n" ;
				else {
					$grille .= "\t\t<td>"
						.'<label for="col'.$colonne.'lig'.$ligne.'">'
						._T('motscroises:ligne',Array('n'=>lettre_grille($ligne))).';'
						._T('motscroises:colonne',Array('n'=>$colonne)).'</label>';
						
					// test l'existence de la variable global correpsonte à cette cellule	
					if (isset($GLOBALS['col'.$colonne.'lig'.$ligne]) and $GLOBALS['col'.$colonne.'lig'.$ligne]!='') 
						$grille.='<input type="text" maxlength="1" value="'.$GLOBALS['col'.$colonne.'lig'.$ligne].'" name="col'.$colonne.'lig'.$ligne.'" id="col'.$colonne.'lig'.$ligne."\" />";
					else
						$grille.='<input type="text" maxlength="1"  name="col'.$colonne.'lig'.$ligne.'" id="col'.$colonne.'lig'.$ligne."\" />";                        
					
					$grille .= "</td>\n" ;		//cloture de la cellule
				}
		} // foreach
                                                    
        $grille=$grille."\t</tr>\n";}		
	
	//fin affichage des lignes
	
	$grille.="</table>\n";
	
	(!$solution) ? $grille.="<br /><input id=\"solution\" name=\"solution[]\" type=\"checkbox\"value=\"1\" /><label for=\"solution\" >"._T('motscroises:afficher_solution')."</label><br />\n
<input type=\"submit\" value=\""._T('motscroises:verifier')."\" name=\"bouton_envoi\" /></form>\n" : $grille.="</div>";

	return $grille;
}

// déchiffre le code source de la grille
function calcul_tableau_grille($texte){
	$texte = trim($texte);
	$tableau = explode("\r", $texte);	

	//ligne par ligne
	$j =0;
	foreach ($tableau as $i){	
		$tableau[$j] = explode('|',trim($i));		//une cellule, c'est beau !
		array_shift($tableau[$j]);
		array_pop($tableau[$j]);
		$j++;
	}
	
	return $tableau;
}


// compare les variables Post avec les valeurs de la solution...
function comparaison_grille($tableau_grille){
    $erreurs=0; $vides=0;
    foreach($tableau_grille as $ligne =>$contenu_ligne){
        $ligne++;
        foreach ($contenu_ligne as $colonne =>$cellule){
            $colonne++;
			
            //compare les valeurs du tableau PHP avec les variables POST
			if ($cellule!='*') {
	            if (trim($GLOBALS["col".$colonne."lig".$ligne])=='') $vides++;
    	        elseif (strtoupper($GLOBALS["col".$colonne."lig".$ligne])!=strtoupper($cellule)) $erreurs++;
			}	
		}
	}
    return array($erreurs, $vides);
}

?>
