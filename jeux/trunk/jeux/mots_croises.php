<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
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
 Idee originale de Ma•eul ROUQUETTE
------------------------------------------------------

separateurs obligatoires : [horizontal], [vertical], [solution]
separateurs optionnels   : [titre], [texte], [config]
parametres de configurations par defaut :
	solution=oui	// Afficher la solution ?
	fondnoir=noir	// couleur des cases noires
	compact=non		// Definitions en format compact ?
	type=0			// types de grilles : 0 ou 1
    vertical=chiffres // on met des chiffres en vertical
    horizontal=lettres // on met des lettres en horizontal
	alphabet=latin1 // Utiliser un alphabet latin simple
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
	$alphabet = jeux_alphabet(jeux_config('alphabet'));
	return $chiffre<=count($alphabet)?$alphabet[$chiffre - 1]:'?';
}

// affiche la grille de mot croises, avec la solution au cas ou
function affichage_grille_mc($tableau_grille, $indexJeux, $form, $solution=false){
	global $jeux_couleurs;
	// les variables de la grille
	$hauteur = sizeof($tableau_grille);
    $largeur = sizeof($tableau_grille[0]);
    $grille = '';
    
    $type_vertical      =   jeux_config('vertical');
    $type_horizontal    =   jeux_config('horizontal');

    // entetes : formulaire + grille
    $grille .= (!$solution)
		? ($form?jeux_form_debut('motscroises', $indexJeux, 'jeux_grille jeux_left', 'post', self()):'')
		: '<div class="jeux_solution">'._T('jeux:solution').' : </div>' ;
    $grille .= '<table class="jeux_grille" cellspacing="0" border="0" summary="'
		. _T('motscroises:table_summary', array('hauteur'=>$hauteur,'largeur'=>$largeur))
		. "\">\n\t<tr>\n\t\t<td class=\"jeux_coin\"></td>\n";
	
	
	// les cellules d'entetes verticales
	for($i = 1; $i<=$largeur; $i++){ 
	   $entete_colonne = $type_vertical == 'lettres' ? lettre_grille($i) :  $i ;
	   $grille .= "\t\t<th scope=\"col\">$entete_colonne</th>\n";
	   }
	$grille .= "\t</tr>\n";		
	
	// debut affichage des lignes
	foreach($tableau_grille as $ligne =>$contenu_ligne){
		$ligne++;
		$entete_ligne = $type_horizontal=='chiffres'?$ligne:lettre_grille($ligne);
		$grille .= "\t<tr>\n\t<th scope=\"row\">".$entete_ligne."</th>\n";	// numeros de ligne
		
		foreach ($contenu_ligne as $colonne =>$cellule){
		    $colonne++;
		    $entete_colonne = $type_vertical == 'lettres' ? lettre_grille($colonne) :  $colonne;
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
				list($id, $name) = jeux_idname($indexJeux, $colonne, 'C', $ligne, 'L'); 
				$value = jeux_form_reponse($indexJeux, $colonne, 'C', $ligne, 'L');
				$value = strlen($value)?' value="'.$value.'"':'';
				$grille .= "\t\t<td$class><label for=\"$name\">"
					. _T('jeux:ligne_n', array('n'=>$entete_ligne)).';'
					. _T('jeux:colonne_n', array('n'=>$entete_colonne)).'</label>'
					. "<input type='text' maxlength='1'$value name='$name' id='$id' /></td>\n" ;
			}
		} // foreach
                                                    
        $grille = $grille."\t</tr>\n";}		
	
	// fin affichage des lignes
	
	$grille.="</table>\n";
	
	if (!$solution) {
		list($id, $name) = jeux_idname($indexJeux, 'SOL'); 
		$grille .= 
		(jeux_config('solution')?"<p><input id='$id' name='$name' type='checkbox' class='jeux_cocher' value='1' /><label for='$id' >"._T('jeux:afficher_solution')."</label></p>\n":'')
		.'<p><input type="submit" value="'._T('jeux:verifier')."\" name='submit' /></p>"
		.($form?jeux_form_fin():'');
	}

	return $grille;
}

// dechiffre le code source de la grille
function calcul_tableau_grille($texte){
	$texte = preg_replace(",\s?[\r\n]+\s?,", "\n", trim($texte));
	$tableau = preg_split("/\n/", $texte);
	$reg = $GLOBALS['meta']['charset'] == 'utf-8' ? '//u' : '//';
	foreach ($tableau as $i=>$valeur) $tableau[$i] = preg_split($reg, trim($valeur), -1, PREG_SPLIT_NO_EMPTY);
	return $tableau;
}

// compare les variables Post avec les valeurs de la solution...
function comparaison_grille($tableau_grille, $indexJeux) {
    $erreurs=0; $vides=0; $total=0;
    foreach($tableau_grille as $ligne =>$contenu_ligne) {
        foreach ($contenu_ligne as $colonne =>$cellule) {
            //compare les valeurs du tableau PHP avec les variables POST
			if ($cellule!='*') {
				$input = jeux_form_reponse($indexJeux, $colonne+1, 'C', $ligne+1, 'L');
				$total++; // nombre de case total
	            if ($input=='') $vides++;
    	         elseif (strtoupper($input)!=strtoupper($cellule)) $erreurs++;
			}	
		}
	}
    return array($erreurs, $vides,$total);
}

// renvoie le nombre d'erreurs et de cases vides
function calcul_erreurs_grille($solution, $indexJeux) {
	if (!jeux_form_correction($indexJeux)) return '';
	list($nbr_erreurs, $nbr_vides,$total) = comparaison_grille($solution, $indexJeux); 
	$id_jeu = _request('id_jeu');
	// on insere le resultat dans la base de donnee
	if ($id_jeu){
	  	include_spip('base/jeux_ajouter_resultat');
		jeux_ajouter_resultat($id_jeu, $total-$nbr_erreurs-$nbr_vides, $total, "erreurs=$nbr_erreurs, vides=$nbr_vides");
	}

	// on retourne ce qu'on affiche
	return '<div class="jeux_erreur">'
		. (($nbr_erreurs==0)?_T('jeux:aucune_erreur'):(
		 ($nbr_erreurs==1)?_T('jeux:une_erreur'):_T("jeux:n_erreurs", array('n'=>$nbr_erreurs))
		))
		. (($nbr_vides==0)?(($nbr_erreurs==0)?'. '._T('jeux:bravo'):''):(
		 ($nbr_vides==1)?' - '._T('jeux:une_vide'):' - '._T("jeux:n_vides", array('n'=>$nbr_vides))
		))
		. '</div><br />';
}

// retourne une liste compactee alphabetique ou numerique
function jeux_listes_compacte($texte, $alpha) {
	$tableau = preg_split("/[\r\n]+/", trim($texte));
	$tableau2 = array(); $i = 0; 
	$a = jeux_alphabet(jeux_config('alphabet'));
	$n = count($a);
	foreach ($tableau as $i=>$valeur) if (($valeur=trim($valeur))!='') {
		$c = $alpha?($i>=$n?'?':$a[$i]):$i+1;
		if ($valeur[strlen($valeur)-1]!='.') $valeur.='.';
		if ($valeur!='-.') $tableau2[] = "<strong>$c.</strong>&nbsp;$valeur";
	}
	return implode(' ', $tableau2);
}

// definitions des mots croises
function affichage_definitions($horizontal, $vertical) {
	if (jeux_config('compact')) return 
		'<p><strong>'._T('motscroises:horizontalement').'&nbsp;</strong>'
		.jeux_listes_compacte($horizontal, jeux_config('horizontal')=='lettres') 
		.'<br /><strong>'._T('motscroises:verticalement').'&nbsp;</strong>'
		.jeux_listes_compacte($vertical, jeux_config('vertical')=='lettres').'</p>';

	// probleme a regler : les listes non compactes ne reprennent pas l'alphabet choisi...
	
	$liste_horizontal = jeux_config('horizontal')=='chiffres'?'<div class="spip jeux_horizontal jeux_liste_chiffres">':'<div class="spip jeux_horizontal jeux_liste_lettres">';
    
	$liste_horizontal .= '<h4 class="spip jeux_grille">'
		._T('motscroises:horizontalement')."</h4>\n".jeux_listes($horizontal).'</div>';
		
	$liste_vertical = jeux_config('vertical')=='lettres'?'<div class="spip jeux_vertical jeux_liste_lettres">':'<div class="spip jeux_vertical jeux_liste_chiffres">';
	$liste_vertical .= '<h4 class="spip jeux_grille">'
		._T('motscroises:verticalement')."</h4>\n".jeux_listes($vertical).'</div>';
	return $liste_horizontal.$liste_vertical;
}

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_mots_croises_init() {
	return "
		solution=oui	// Afficher la solution ?
		fondnoir=noir	// couleur des cases noires
		compact=non		// Definitions en format compact ?
		type=0			// types de grilles : 0 ou 1
		vertical=chiffres // on met des chiffres en vertical
		horizontal=lettres // on met des lettres en horizontal
		alphabet=latin1 // Utiliser un alphabet latin simple
	";
}
	
// decode une grille de mots croises 
// traitement du jeu : jeu_{mon_jeu}()
function jeux_mots_croises($texte, $indexJeux, $form=true) {
    
	$horizontal = $vertical = $solution = $html = false;
	$titre = _T('motscroises:titre');
    // parcourir tous les #SEPARATEURS
	$tableau = jeux_split_texte('mots_croises', $texte);
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_HORIZONTAL) $horizontal = $tableau[$i+1];
	  elseif ($valeur==_JEUX_VERTICAL) $vertical = $tableau[$i+1];
	  elseif ($valeur==_JEUX_SOLUTION) $solution = calcul_tableau_grille($tableau[$i+1]);
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	}
	return 	'<div class="mots_croises">'
			. calcul_erreurs_grille($solution, $indexJeux)
			. affichage_grille_mc($solution, $indexJeux, $form)
			. affichage_definitions($horizontal, $vertical)
	// solution
			. (jeux_form_reponse($indexJeux, 'SOL') ? affichage_grille_mc($solution, $indexJeux, $form, true) : '')
			. '</div><br class="jeux_nettoyeur"/>';
}
?>
