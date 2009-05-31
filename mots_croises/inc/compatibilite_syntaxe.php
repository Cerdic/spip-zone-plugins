<?php
// on garde tout ca pour compatibilite avec l'ancienne syntaxe

// transforme les listes verticales/horizontale spip en html
function mots_croises_listes_vieille_syntaxe($texte) {
	$texte = preg_replace('/ *-#/','<li>',$texte);
	$texte = implode("</li>\n", preg_split("/\n*\r*\n+\r*\n*/",$texte));
	return "<ol>$texte</li></ol>"; 
}

// déchiffre le code source de la grille
function calcul_tableau_grille_vieille_syntaxe($texte){
	$texte = trim($texte);
	$tableau = explode("\r", $texte);	
	//ligne par ligne
	$j =0;
	foreach ($tableau as $i){	
		$tableau[$j] = explode('|',trim($i));	// une cellule, c'est beau !
		array_shift($tableau[$j]);
		array_pop($tableau[$j]);
		$j++;
	}
	return $tableau;
}


//fonction principale
function mots_croises_vieille_syntaxe($texte) {	
	if (! preg_match("/<grille>|<\/grille>/",$texte)) return $texte;
	include_spip('inc/gestion_grille');
	$tableau = preg_split("/<grille>|<\/grille>/",$texte);	// sera uniquement le tableau spip, mais on attend pour le moment
	$j =0;
	
	foreach ($tableau as $i){
			if ($j!=0 and $j!=count($tableau)-1){	// pas les extremites du tableau
				$tableau_php = calcul_tableau_grille_vieille_syntaxe($tableau[$j]);
			
				// calcul erreur
				if ($GLOBALS["bouton_envoi"] == '') $erreur='';
				else {
					list($nbr_erreurs, $nbr_vides) = comparaison_grille($tableau_php); 
					$erreur = '<strong class="erreur">';
					$erreur .= ($nbr_erreurs==0)?_T('motscroises:aucune_erreur'):(
					 ($nbr_erreurs==1)?_T('motscroises:une_erreur'):_T("motscroises:nombre_erreurs", Array('err'=>$nbr_erreurs))
					);
					$erreur .= ($nbr_vides==0)?(($nbr_erreurs==0)?'. '._T('motscroises:bravo'):''):(
					 ($nbr_vides==1)?' - '._T('motscroises:une_vide'):' - '._T("motscroises:nombre_vides", Array('vid'=>$nbr_vides))
					);
					$erreur.='</strong><br />';
				}
				//fin calcul erreur
				
				$tableau[$j] = code_echappement(_GRILLE_.$erreur.affichage_grille($tableau_php)._GRILLE_);	
				}
			
			$j++;
			}
	
	$texte = implode($tableau);
	
	// definitions horizontales
	$tableau = preg_split("/<horizontal>|<\/horizontal>/",$texte);
	$j = 0;
	foreach ($tableau as $i){
		if ($j!=0 and $j!=count($tableau)-1)	// pas les extremites du tableau
				$tableau[$j] = code_echappement(_GRILLE_.'<div class="spip horizontal"><h4 class="spip grille">'
					._T('motscroises:horizontalement')."</h4>\n".mots_croises_listes_vieille_syntaxe(trim($i))."</div>"._GRILLE_);
		$j++;
	}
	$texte = implode($tableau);
	
	// definitions verticales
	$tableau = preg_split("/<vertical>|<\/vertical>/",$texte);
	$j = 0;
	foreach ($tableau as $i){
		if ($j!=0 and $j!=count($tableau)-1)	// pas les extremites du tableau
				$tableau[$j] = code_echappement(_GRILLE_.'<div class="spip vertical"><h4 class="spip grille">'
					._T('motscroises:verticalement')."</h4>\n"
					.mots_croises_listes_vieille_syntaxe(trim($i))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>"._GRILLE_); // Bug IE ?
		$j++;
	}
	$texte = implode($tableau);
	
	// solution
	if ($GLOBALS["solution"][0] == 1) $texte .= code_echappement(_GRILLE_.affichage_grille($tableau_php, true)._GRILLE_);

	return $texte;
}

?>