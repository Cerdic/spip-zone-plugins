<?php
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

 Insere une grille de mots croises dans vos articles !
------------------------------------------------------
 Idee originale de Maieul ROUQUETTE
------------------------------------------------------

separateurs obligatoires : [horizontal], [vertical], [solution]
separateurs optionnels   : [titre], [texte], [config]
parametres de configurations par defaut :
	solution=oui	// Afficher la solution ?
	fondnoir=noir	// couleur des cases noires
	compact=non		// Definitions en format compact ?
	type=0			// types de grilles : 0 ou 1

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[horizontal]
	Definition 1
	Definition 2. Definition 3
	Definition 4. Definition 5
	
	[vertical]
	Definition 1
	Definition 2
	Definition 3
	
	[solution]
	aaa
	b*g
	a*d
</jeux>

*/

// retourne la lettre correspondant au chiffre
function lettre_grille($chiffre) {
	$alphabet = _T('jeux:alphabet');
	return $alphabet[$chiffre-1];
}

// affiche la grille de mot croises, avec la solution au cas ou
function affichage_grille_mc($tableau_grille, $indexJeux, $solution=false){
	$jeux_couleurs = _request('jeux_couleurs');
	
	// les variables de la grille
	$hauteur = sizeof($tableau_grille);
    $largeur = sizeof($tableau_grille[0]);
    $grille = '';

    // entetes : formulaire + grille
    $grille .= (!$solution)? jeux_form_debut('motscroises', $indexJeux, 'jeux_grille jeux_left', 'post', self())
		: '<p class="jeux_solution">'._T('jeux:solution').' : </p>' ;
    $grille .= '<table class="jeux_grille" cellspacing="0" border="0" summary="'
		. _T('motscroises:table_summary',Array('hauteur'=>$hauteur,'largeur'=>$largeur))
		. "\">\n\t<tr>\n\t\t<td class=\"jeux_coin\"></td>\n";
	
	
	// les cellules d'entetes verticales
	for($i = 1; $i<=$largeur; $i++) $grille .= "\t\t<th scope=\"col\">$i</th>\n";
	$grille .= "\t</tr>\n";		
	
	// debut affichage des lignes
	foreach($tableau_grille as $ligne =>$contenu_ligne){
		$ligne++;
		$grille .= "\t<tr>\n\t<th scope=\"row\">".lettre_grille($ligne)."</th>\n";	// numeros de ligne
		
		foreach ($contenu_ligne as $colonne =>$cellule){
		    $colonne++;
			$class = $ligne==$hauteur?($colonne==$largeur?' class="jeux_bas jeux_droite"':' class="jeux_bas"'):($colonne==$largeur?' class="jeux_droite"':'');
			$classnoir = ' class="jeux_noir' . ($ligne==$hauteur?($colonne==$largeur?' jeux_bas jeux_droite':' jeux_bas'):($colonne==$largeur?' jeux_droite':'')) . '"';
		    // s'il s'agit d'un noir
		    if ($cellule == "*") { 
					$noires = $jeux_couleurs[jeux_config('fondnoir')];
					$noires = "rgb($noires[0], $noires[1], $noires[2])";
			    	$grille .= "\t\t<td$classnoir style=\"background-color:$noires; color:$noires;\">*</td>\n";
				}
				else if ($solution)
					$grille .= "\t\t<td$class>$cellule</td>\n" ;
				else {
					$name = 'GR'.$indexJeux.'x'.$colonne.'x'.$ligne;
					$valeur = _request($name);
					$grille .= "\t\t<td$class><label for=\"$name\">"
						. _T('jeux:ligne_n',Array('n'=>lettre_grille($ligne))).';'
						. _T('jeux:colonne_n',Array('n'=>$colonne)).'</label>'
						. '<input type="text" maxlength="1" '
						. ((isset($valeur) and $valeur!='')? 'value="'.$valeur:'')
						.'" name="'.$name.'" id="'.$name.'" />'
						. "</td>\n" ;
				}
		} // foreach
                                                    
        $grille = $grille."\t</tr>\n";}		
	
	// fin affichage des lignes
	
	$grille.="</table>\n";
	
	if (!$solution) $grille .= 
		(jeux_config('solution')?"<p><input id=\"affiche_solution_$indexJeux\" name=\"affiche_solution_{$indexJeux}[]\" type=\"checkbox\" class=\"jeux_cocher\" value=\"1\" /><label for=\"affiche_solution_$indexJeux\" >"._T('jeux:afficher_solution')."</label></p>\n":'')
		.'<p><input type="submit" value="'._T('jeux:verifier')."\" name=\"bouton_envoi_$indexJeux\" /></p>".jeux_form_fin();

	return $grille;
}

// dechiffre le code source de la grille
function calcul_tableau_grille($texte){
	$texte = preg_replace(",\s?[\r\n]+\s?,", "\n", trim($texte));
	$tableau = split("\n", $texte);	
	foreach ($tableau as $i=>$valeur) $tableau[$i] = preg_split('//', trim($valeur), -1, PREG_SPLIT_NO_EMPTY);
	return $tableau;
}

// compare les variables Post avec les valeurs de la solution...
function comparaison_grille($tableau_grille, $indexJeux) {
    $erreurs=0; $vides=0;
    foreach($tableau_grille as $ligne =>$contenu_ligne) {
        foreach ($contenu_ligne as $colonne =>$cellule) {
            //compare les valeurs du tableau PHP avec les variables POST
			if ($cellule!='*') {
				$input = trim(_request('GR'.$indexJeux.'x'.($colonne+1).'x'.($ligne+1)));
	            if ($input=='') $vides++;
    	         elseif (strtoupper($input)!=strtoupper($cellule)) $erreurs++;
			}	
		}
	}
    return array($erreurs, $vides);
}

// renvoie le nombre d'erreurs et de cases vides
function calcul_erreurs_grille($solution, $indexJeux) {
	if (_request("bouton_envoi_$indexJeux") == '') return '';
	else {
	  list($nbr_erreurs, $nbr_vides) = comparaison_grille($solution, $indexJeux); 
	  return '<p class="jeux_erreur">'
		. (($nbr_erreurs==0)?_T('jeux:aucune_erreur'):(
		 ($nbr_erreurs==1)?_T('jeux:une_erreur'):_T("jeux:n_erreurs", Array('n'=>$nbr_erreurs))
		))
		. (($nbr_vides==0)?(($nbr_erreurs==0)?'. '._T('jeux:bravo'):''):(
		 ($nbr_vides==1)?' - '._T('jeux:une_vide'):' - '._T("jeux:n_vides", Array('n'=>$nbr_vides))
		))
		. '</p><br />';
	}
}

// retourne une liste compactee alphabetique ou numerique
function jeux_listes_compacte($texte, $alpha) {
	$tableau = preg_split("/[\r\n]+/", trim($texte));	
	$tableau2 = array(); $i=0; $a=_T('jeux:alphabet');
	foreach ($tableau as $i=>$valeur) if (($valeur=trim($valeur))!='') {
		$c=$alpha?$a[$i]:$i+1;
		if ($valeur[strlen($valeur)-1]!='.') $valeur.='.';
		if ($valeur!='-.') $tableau2[] = "<strong>$c.</strong>&nbsp;$valeur";
	}
	return implode(' ', $tableau2);
}

// definitions des mots croises
function affichage_definitions($horizontal, $vertical) {
 if (jeux_config('compact')) return 
 		'<p><strong>'._T('motscroises:horizontalement').'&nbsp;</strong>'
		.jeux_listes_compacte($horizontal, true) 
 		.'<br /><strong>'._T('motscroises:verticalement').'&nbsp;</strong>'
		.jeux_listes_compacte($vertical, false).'</p>';
 else return '<div class="spip jeux_horizontal"><h4 class="spip jeux_grille">'
		._T('motscroises:horizontalement')."</h4>\n".jeux_listes($horizontal).'</div>'
	. '<div class="spip jeux_vertical"><h4 class="spip jeux_grille">'
		._T('motscroises:verticalement')."</h4>\n".jeux_listes($vertical).'</div>';
}


// decode une grille de mots croises 
function jeux_mots_croises($texte, $indexJeux) {
	$horizontal = $vertical = $solution = $html = false;
	$titre = _T('motscroises:titre');
	
    // parcourir tous les #SEPARATEURS
	$tableau = jeux_split_texte('mots_croises', $texte);
	// configuration par defaut
	jeux_config_init("
		solution=oui	// Afficher la solution ?
		fondnoir=noir	// couleur des cases noires
		compact=non		// Definitions en format compact ?
		type=0			// types de grilles : 0 ou 1
	", false);
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_HORIZONTAL) $horizontal = $tableau[$i+1];
	  elseif ($valeur==_JEUX_VERTICAL) $vertical = $tableau[$i+1];
	  elseif ($valeur==_JEUX_SOLUTION) $solution = calcul_tableau_grille($tableau[$i+1]);
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	}
	$affiche_solution = _request('affiche_solution_'.$indexJeux);
	return 	'<div class="mots_croises">'
			. calcul_erreurs_grille($solution, $indexJeux)
			. affichage_grille_mc($solution, $indexJeux)
			. affichage_definitions($horizontal, $vertical)
	// solution
			. (($affiche_solution[0] == 1)? affichage_grille_mc($solution, $indexJeux, true) : '')
			. '</div><br class="jeux_nettoyeur"/>';
}
?>