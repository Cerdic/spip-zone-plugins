<?php
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
/*

 Insere une grille de mots croises dans vos articles !
------------------------------------------------------
 Idee originale de Maieul ROUQUETTE
------------------------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : #HORIZONTAL, #VERTICAL, #SOLUTION
separateurs optionnels   : #TITRE, #HTML

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	#HORIZONTAL
	Definition 1
	Definition 2. Definition 3
	Definition 4. Definition 5
	
	#VERTICAL
	Definition 1
	Definition 2
	Definition 3
	
	#SOLUTION
	aaa
	b*g
	a*d
</jeux>

*/

//retourne la lettre correspondant au chiffre
function lettre_grille($texte) {
	$alphabet="*ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	return $alphabet[$texte];
}

//affiche la grille de mot croises, avec la solution au cas ou
function affichage_grille_mc($tableau_grille, $indexJeux, $solution=false){
	
	// les variables de la grille
	$hauteur = sizeof($tableau_grille);
    $largeur = sizeof($tableau_grille[0]);
    $grille = '';

    // entetes : formulaire + grille
    $grille .= (!$solution)? "<form class=\"grille\" action=\"".self()."\" method=\"post\">\n" 
		: "<div class=\"solution\"><h2 class=\"spip\">"._T('motscroises:solution')." : </h2>" ;
    $grille .= '<table class="grille" cellspacing="0" border="0" summary="'
		. _T('motscroises:table_summary',Array('hauteur'=>$hauteur,'largeur'=>$largeur))
		. "\">\n\t<tr>\n\t\t<td class=\"coin\"></td>\n";
	
	
	//les cellules d'entetes verticales
	for($i = 1; $i<=$largeur; $i++) $grille .= "\t\t<th scope=\"col\">$i</th>\n";
	$grille .= "\t</tr>\n";		
	
	//debut affichage des lignes
	foreach($tableau_grille as $ligne =>$contenu_ligne){
		$ligne++;
		$grille .= "\t<tr>\n\t<th scope=\"row\">".lettre_grille($ligne)."</th>\n";	// numeros de ligne
		
		foreach ($contenu_ligne as $colonne =>$cellule){
		    $colonne++;
		    //s'il s'agit d'un noir
		    if ($cellule == "*") 
		    	$grille .= "\t\t<td class=\"noir\">*</td>\n";
				else if ($solution)
					$grille .= "\t\t<td>$cellule</td>\n" ;
				else {
					$name = 'GR'.$indexJeux.'x'.$colonne.'x'.$ligne;
					$grille .= "\t\t<td><label for=\"".$name.'">'
						. _T('motscroises:ligne',Array('n'=>lettre_grille($ligne))).';'
						. _T('motscroises:colonne',Array('n'=>$colonne)).'</label>'
						. '<input type="text" maxlength="1" '
						. ((isset($GLOBALS[$name]) and $GLOBALS[$name]!='')? 'value="'.$GLOBALS[$name]:'')
						.'" name="'.$name.'" id="'.$name.'" />'
						. "</td>\n" ;
				}
		} // foreach
                                                    
        $grille = $grille."\t</tr>\n";}		
	
	// fin affichage des lignes
	
	$grille.="</table>\n";
	
	(!$solution) ? $grille.="<br /><input id=\"solution\" name=\"solution[]\" type=\"checkbox\"value=\"1\" /><label for=\"solution\" >"._T('motscroises:afficher_solution')."</label><br />\n
<input type=\"submit\" value=\""._T('motscroises:verifier')."\" name=\"bouton_envoi\" /></form>\n" : $grille.="</div>";

	return $grille;
}

// dechiffre le code source de la grille
function calcul_tableau_grille($texte){
	$tableau = explode("\r", trim($texte));	
	foreach ($tableau as $i=>$v) $tableau[$i] = preg_split('//', trim($v), -1, PREG_SPLIT_NO_EMPTY);
	return $tableau;
}

// compare les variables Post avec les valeurs de la solution...
function comparaison_grille($tableau_grille, $indexJeux){
    $erreurs=0; $vides=0;
    foreach($tableau_grille as $ligne =>$contenu_ligne){
        $ligne++;
        foreach ($contenu_ligne as $colonne =>$cellule){
            $colonne++;
			
            //compare les valeurs du tableau PHP avec les variables POST
			if ($cellule!='*') {
				$input = trim($GLOBALS['GR'.$indexJeux.'x'.$colonne.'x'.$ligne]);
	            if ($input=='') $vides++;
    	         elseif (strtoupper($input)!=strtoupper($cellule)) $erreurs++;
			}	
		}
	}
    return array($erreurs, $vides);
}

// renvoie le nombre d'erreurs de de cases vides
function calcul_erreurs_grille($solution, $indexJeux) {
	if ($GLOBALS["bouton_envoi"] == '') return '';
	else {
	  list($nbr_erreurs, $nbr_vides) = comparaison_grille($solution, $indexJeux); 
	  return '<strong class="erreur">'
		. (($nbr_erreurs==0)?_T('motscroises:aucune_erreur'):(
		 ($nbr_erreurs==1)?_T('motscroises:une_erreur'):_T("motscroises:nombre_erreurs", Array('err'=>$nbr_erreurs))
		))
		. (($nbr_vides==0)?(($nbr_erreurs==0)?'. '._T('motscroises:bravo'):''):(
		 ($nbr_vides==1)?' - '._T('motscroises:une_vide'):' - '._T("motscroises:nombre_vides", Array('vid'=>$nbr_vides))
		))
		. '</strong><br />';
	}
}

// decode une grille de mots croises 
function jeux_mots_croises($texte, $indexJeux) {
	$tableau = preg_split('/('._JEUX_TITRE.'|'._JEUX_HORIZONTAL.'|'._JEUX_VERTICAL.'|'._JEUX_SOLUTION.'|'._JEUX_HTML.')/', 
			trim(_JEUX_HTML.$texte), -1, PREG_SPLIT_DELIM_CAPTURE);
	$horizontal = $vertical = $solution = $html = false;
	$titre = _T('motscroises:titre');
	
	foreach($tableau as $i => $v){
  	 $v = trim($v);
	 if ($v==_JEUX_TITRE) $titre = trim($tableau[$i+1]);
	  elseif ($v==_JEUX_HORIZONTAL) $horizontal = jeux_listes($tableau[$i+1]);
	  elseif ($v==_JEUX_VERTICAL) $vertical = jeux_listes($tableau[$i+1]);
	  elseif ($v==_JEUX_SOLUTION) $solution = calcul_tableau_grille($tableau[$i+1]);
	  elseif ($v==_JEUX_HTML) $html .= trim($tableau[$i+1]);
	}

	return calcul_erreurs_grille($solution, $indexJeux)
			. affichage_grille_mc($solution, $indexJeux)
	// definitions	
			. '<div class="spip horizontal"><h4 class="spip grille">'
					._T('motscroises:horizontalement')."</h4>\n".$horizontal.'</div>'
			. '<div class="spip vertical"><h4 class="spip grille">'
					._T('motscroises:verticalement')."</h4>\n".$vertical.'</div>'
	// solution
			. (($GLOBALS["solution"][0] == 1)? affichage_grille_mc($solution, $indexJeux, true) : '');
}
?>