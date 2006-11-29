<?php
/*
insere une grille de mots croises dans vos articles !
-----------------------------------------------------

balises : <jeux></jeux>
separateurs obligatoires : #HORIZONTAL, #VERTICAL, #SOLUTION
separateurs optionnels   : #TITRE, #HTML

Esemble de syntaxe dans l'article :
-----------------------------------

<jeux>
	#SUDOKU
	98-4----3
	64-3-----
	--3--5--1
	8-4---365
	2-6---8-9
	159---2-7
	3--8--1--
	-----6-38
	5----2-94
	
	#SOLUTION
	985421673
	641378952
	723965481
	874219365
	236547819
	159683247
	397854126
	412796538
	568132794
</jeux>

*/
//affiche la grille de sudoku, en format solution au cas ou
function affichage_sudoku($tableau_sudoku, $solution=false){
	
	// les variables de la grille
	$hauteur =sizeof($tableau_sudoku);
    $largeur= sizeof($tableau_sudoku[0]);
    $grille='';
    
    // entetes : formulaire + grille
    $grille .= (!$solution)? "<form class=\"grille\" action=\"".self()."\" method=\"post\">\n" 
		: "<div class=\"solution\"><h2 class=\"spip\">"._T('sudoku:solution')." : </h2>" ;
    $grille .= '<table class="grille" cellspacing="0" border="0" summary="'
		. _T('sudoku:table_summary',Array('hauteur'=>$hauteur,'largeur'=>$largeur)) . "\">\n";
    
	//debut affichage des lignes
	foreach($tableau_sudoku as $ligne => $contenu_ligne){
		$ligne++;
		$grille .= "\t<tr>\n\t";
		
		foreach ($contenu_ligne as $colonne =>$cellule){
		    $colonne++;
		    //s'il s'agit pas d'un espace
		    if ($cellule != "-") 
		    	$grille .= "\t\t<td><strong>$cellule</strong></td>\n";
				else if ($solution)
					$grille .= "\t\t<td>$cellule</td>\n" ;
				else {
					$grille .= "\t\t<td>"
						.'<label for="GR'.$colonne.'x'.$ligne.'">'
						._T('sudoku:ligne',Array('n'=>$ligne)).';'
						._T('sudoku:colonne',Array('n'=>$colonne)).'</label>';
						
					// test l'existence de la variable global correpsonte à cette cellule	
					if (isset($GLOBALS['GR'.$colonne.'x'.$ligne]) and $GLOBALS['GR'.$colonne.'x'.$ligne]!='') 
						$grille.='<input type="text" maxlength="1" value="'.$GLOBALS['GR'.$colonne.'x'.$ligne].'" name="GR'.$colonne.'x'.$ligne.'" id="GR'.$colonne.'x'.$ligne."\" />";
					else
						$grille.='<input type="text" maxlength="1"  name="GR'.$colonne.'x'.$ligne.'" id="GR'.$colonne.'x'.$ligne."\" />";                        
					
					$grille .= "</td>\n" ;		//cloture de la cellule
				}
		} // foreach
                                                    
        $grille = $grille."\t</tr>\n";}		
	
	//fin affichage des lignes
	
	$grille.="</table>\n";
	
	(!$solution) ? $grille.="<br /><input id=\"solution\" name=\"solution[]\" type=\"checkbox\"value=\"1\" /><label for=\"solution\" >"._T('sudoku:afficher_solution')."</label><br />\n
<input type=\"submit\" value=\""._T('sudoku:verifier')."\" name=\"bouton_envoi\" /></form>\n" : $grille.="</div>";

	return $grille;
}

// dechiffre le code source de la grille
function calcul_tableau_sudoku($texte){
	$tableau = explode("\r", trim($texte));	
	foreach ($tableau as $i=>$v) $tableau[$i] = preg_split('//', trim($v), -1, PREG_SPLIT_NO_EMPTY);
	return $tableau;
}

// compare les variables Post avec les valeurs de la solution...
function comparaison_sudoku($tableau_sudoku){
    $erreurs=0; $vides=0;
    foreach($tableau_sudoku as $ligne =>$contenu_ligne){
        $ligne++;
        foreach ($contenu_ligne as $colonne =>$cellule){
            $colonne++;
			
            //compare les valeurs du tableau PHP avec les variables POST
			if ($cellule!='*') {
				$input = trim($GLOBALS["GR".$colonne."x".$ligne]);
	            if ($input=='') $vides++;
    	         elseif (strtoupper($input)!=strtoupper($cellule)) $erreurs++;
			}	
		}
	}
    return array($erreurs, $vides);
}

// renvoie le nombre d'erreurs de de cases vides
function calcul_erreurs_sudoku($solution) {
	if ($GLOBALS["bouton_envoi"] == '') return '';
	else {
	  list($nbr_erreurs, $nbr_vides) = comparaison_sudoku($solution); 
	  return '<strong class="erreur">'
		. (($nbr_erreurs==0)?_T('sudoku:aucune_erreur'):(
		 ($nbr_erreurs==1)?_T('sudoku:une_erreur'):_T("sudoku:nombre_erreurs", Array('err'=>$nbr_erreurs))
		))
		. (($nbr_vides==0)?(($nbr_erreurs==0)?'. '._T('sudoku:bravo'):''):(
		 ($nbr_vides==1)?' - '._T('sudoku:une_vide'):' - '._T("sudoku:nombre_vides", Array('vid'=>$nbr_vides))
		))
		. '</strong><br />';
	}
}

// decode une grille de sudoku 
function jeux_sudoku($texte) { 
	$tableau = preg_split('/('._JEUX_TITRE.'|'._JEUX_SUDOKU.'|'._JEUX_SOLUTION.'|'._JEUX_HTML.')/', 
			trim(_JEUX_HTML.$texte), -1, PREG_SPLIT_DELIM_CAPTURE);
	$titre = $sudoku = $solution = $html = false;
	foreach($tableau as $i => $v){
	 $v = trim($v);
//  	 echo "$i. ", trim($v), '<br>';
	 if ($v==_JEUX_TITRE) $titre = trim($tableau[$i+1]);
	  elseif ($v==_JEUX_SUDOKU) $sudoku = calcul_tableau_sudoku($tableau[$i+1]);
	  elseif ($v==_JEUX_SOLUTION)$solution = calcul_tableau_sudoku($tableau[$i+1]);
	  elseif ($v==_JEUX_HTML) $html .= trim($tableau[$i+1]);
	}
// print_r($tableau);
	// trouver un titre, coute que coute...
//	if (!$titre) $titre = jeux_recupere_le_titre($chaine, '<intro>', '</intro>');
	if (!$titre) $titre = _T('sudoku:titre');
	
	return calcul_erreurs_sudoku($solution)
			. affichage_sudoku($sudoku)
	// solution
			. (($GLOBALS["solution"][0] == 1)? affichage_sudoku($solution, true) : '');
}
?>