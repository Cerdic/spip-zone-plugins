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
-----------------------------------------------------

separateurs obligatoires : [sudoku], [solution]
separateurs optionnels   : [titre], [texte], [config]
parametres de configurations par defaut :
	solution=oui	// Afficher la solution ?
	regle=non	// Afficher la regle du jeu ?

Tailles de sudoku acceptees : 4x4, 6x6, 9x9

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[sudoku]
	-2
	--5--4
	3--5
	----3
	--1--6
	6--1
	[solution]
	423615
	165324
	314562
	256431
	531246
	642153
</jeux>

*/
// affiche la grille de sudoku, en format solution au cas ou...
function affichage_sudoku($tableau_sudoku, $indexJeux, $solution=false){

	// les variables de la grille
	$largeur = $hauteur = sizeof($tableau_sudoku);
	switch ($largeur) {
	 case 4 : $interh = $interv = '24'; $li = $lj = 2; break;
	 case 6 : $interh = '36'; $interv = '246'; $lj = 3; $li = 2; break;
	 case 9 : $interh = $interv = '369'; $li = $lj = 3; break;
	 default : return '<p class="jeux_erreur">'._T('jeux:erreur_taille_grille').' : </p>' ;
	}
    $grille='';

    // entetes : formulaire + grille
    $grille .= (!$solution)? jeux_form_debut('sudoku', $indexJeux, 'jeux_grille', 'post', self())
		: '<p class="jeux_solution">'._T('jeux:solution').' : </p>' ;
    $grille .= '<table class="jeux_grille  sudoku" cellspacing="0" border="0" summary="'
		. _T('sudoku:table_summary',Array('hauteur'=>$hauteur,'largeur'=>$largeur)) . "\">\n";
    
	// debut affichage des lignes
	foreach($tableau_sudoku as $ligne => $contenu_ligne){
		$ligne++;
		$grille .= "\t<tr>\n\t";
		
		foreach ($contenu_ligne as $colonne =>$cellule){
		    $colonne++; 
			$class = preg_match(",[$interh],", $colonne)?(preg_match(",[$interv],", $ligne)?' class="jeux_bas jeux_droite"':' class="jeux_droite"'):(preg_match(",[$interv],", $ligne)?' class="jeux_bas"':'');
//				: ($ligne==$hauteur?($colonne==$largeur?' class="jeux_bas jeux_droite"':' class="jeux_bas"'):($colonne==$largeur?' class="jeux_droite"':''))
//			);
		    // s'il s'agit pas d'un espace
		    if ($cellule != '-') 
		    	$grille .= "\t\t<td$class><strong>$cellule</strong></td>\n";
			else if ($solution)
				$grille .= "\t\t<td$class>$cellule</td>\n" ;
			else {
				$name = 'GR'.$indexJeux.'x'.$colonne.'x'.$ligne;
				$grille .= "\t\t<td$class><label for=\"$name\">"
					._T('jeux:ligne_n',Array('n'=>$ligne)).';'
					._T('jeux:colonne_n',Array('n'=>$colonne)).'</label>'
					. '<input type="text" maxlength="1" '
					. ((isset($_POST[$name]) and $_POST[$name]!='')? 'value="'.$_POST[$name]:'')
					.'" name="'.$name.'" id="'.$name.'" />'
					. "</td>\n" ;
			}
		} // foreach
                                                    
        $grille = $grille."\t</tr>\n";}		
	
	// fin affichage des lignes
	
	$grille.="</table>\n";

	
	if (!$solution) $grille .= 
		(jeux_config('regle')?'<p class="jeux_regle">'.definir_puce()._T('sudoku:regle',Array('hauteur'=>$li,'largeur'=>$lj, 'max'=>$largeur)).'</p>' : '')
		.(jeux_config('solution')?"<p><input id=\"affiche_solution_$indexJeux\" name=\"affiche_solution_{$indexJeux}[]\" type=\"checkbox\" class=\"jeux_cocher\" value=\"1\" /><label for=\"affiche_solution_$indexJeux\" >"._T('jeux:afficher_solution')."</label></p>\n":'')
		.'<p><input type="submit" value="'._T('jeux:verifier_validite')."\" name=\"bouton_envoi_$indexJeux\" /></p>".jeux_form_fin();

	return $grille;
}

// dechiffre le code source de la grille
function calcul_tableau_sudoku($texte){
	$texte = preg_replace(",\s*[\r\n]+\s*,", "\n", trim($texte));
	// arggh les raccourcis SPIP... TODO : voir pkoi (1.93)
	$texte = str_replace('&mdash;', '--', $texte); 
	$tableau = split("\n", $texte);	
	$hauteur = count($tableau);
	foreach ($tableau as $i=>$valeur) {
		if (strlen($valeur)) $valeur .= str_repeat('-', $hauteur-strlen($valeur));
		$tableau[$i] = preg_split('//', trim($valeur), -1, PREG_SPLIT_NO_EMPTY);
	}
	return $tableau;
}

// valide si la lsite est composee de chiffres tous differents
function sudoku_valide_liste($liste) {
	// echo"<br>", join(', ',$liste);
	$chiffres = "123456789";
	foreach($liste as $cell) if ($cell!='-') {
		if ($chiffres[$cell]=='*') return false; else $chiffres[$cell]='*';
	}
	// echo ":ok";
	return true;
}

// valide si la grille de sudoku est valide
function sudoku_valide_grille($tableau_sudoku) {
	$taille = sizeof($tableau_sudoku);
	foreach($tableau_sudoku as $ligne) if (!sudoku_valide_liste($ligne)) return false;
	for ($i=0; $i<$taille; $i++) {
		$colonne=false;
		foreach($tableau_sudoku as $ligne) $colonne[] = $ligne[$i];
		if (!sudoku_valide_liste($colonne)) return false;
	}	
	switch ($taille) {
	 case 4 : $ii = 2; $li = 2; $ij = 2; $lj = 2; break;
	 case 6 : $ii = 3; $li = 2; $ij = 2; $lj = 3; break;
	 case 9 : $ii = 3; $li = 3; $ij = 3; $lj = 3; break;
	}
	for ($i=0; $i<$ii; $i++) for ($j=0; $j<$ij; $j++) {
		$zone=false;
		for ($x=0; $x<$li; $x++) for ($y=0; $y<$lj; $y++) $zone[] = $tableau_sudoku[$i*$li+$x][$j*$lj+$y];
		if (!sudoku_valide_liste($zone)) return false;
	}
	return true;
}

// retourne les erreurs et les cases vides eventuelles
function sudoku_validite($tableau_sudoku, $solution, $indexJeux) {
    $vides=0;
    foreach($tableau_sudoku as $ligne => $contenu_ligne) {
        foreach ($contenu_ligne as $colonne => $cellule) {
			$input = trim(_request('GR'.$indexJeux.'x'.($colonne+1).'x'.($ligne+1)));
			if ($input=='' && $cellule=='-') $vides++;
			if ($input!='' && $cellule=='-') $tableau_sudoku[$ligne][$colonne] = $input;
		}
	}
    return array(sudoku_valide_grille($tableau_sudoku), $vides);
}

// renvoie la validite et le nombre de cases vides
function calcul_erreurs_sudoku($tableau_sudoku, $solution, $indexJeux) {
	if (_request("bouton_envoi_$indexJeux") == '') return '';
	else {
	  list($valide, $nbr_vides) = sudoku_validite($tableau_sudoku, $solution, $indexJeux); 
	  return '<p class="jeux_erreur">'
		. _T('jeux:grille_'.($valide?'':'in').'valide')
		. (($nbr_vides==0)?(($nbr_erreurs==0)?'. '._T('jeux:bravo'):''):(
		 ($nbr_vides==1)?' - '._T('jeux:une_vide'):' - '._T("jeux:n_vides", Array('n'=>$nbr_vides))
		))
		. '</p><br />';
	}
}

// decode une grille de sudoku 
function jeux_sudoku($texte, $indexJeux) { 
	$sudoku = $solution = $titre = $html = false;
    // parcourir tous les #SEPARATEURS
	$tableau = jeux_split_texte('sudoku', $texte);
	// configuration par defaut
	jeux_config_init("
		solution=oui	// Afficher la solution ?
		regle=non	// Afficher la regle ?
	", false);
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_SUDOKU) $sudoku = calcul_tableau_sudoku($tableau[$i+1]);
	  elseif ($valeur==_JEUX_SOLUTION) $solution = calcul_tableau_sudoku($tableau[$i+1]);
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	}
	
	return  ($titre?"<p class=\"jeux_titre sudoku_titre\">$titre</p>":'')
			. calcul_erreurs_sudoku($sudoku, $solution, $indexJeux)
			. affichage_sudoku($sudoku, $indexJeux)
	// solution
			. (($_POST['affiche_solution_'.$indexJeux][0] == 1)? affichage_sudoku($solution, $indexJeux, true) : '');
}
?>