<?php

	//transforme les listes verticales/horizontale spip en html
function mot_croises_listes ($texte){
	$texte = preg_replace('/ *-#/','<li>',$texte);
	$texte = implode("</li>\n", preg_split("/\n*\r*\n+\r*\n*/",$texte));
	return "<ol>$texte</li></ol>"; 
}
	
function mot_croises_pre_propre($texte){ return $texte;
	if (! preg_match("/<grille>|<\/grille>/",$texte)) return $texte;
	
	include_spip('inc/gestion_grille');
	
	$tableau = preg_split("/<grille>|<\/grille>/",$texte);					//sera uniquement le tableau spip, mais on attend pour le moment
	$j =0;
	
	foreach ($tableau as $i){
			if ($j!=0 and $j!=count($tableau)-1){	//pas les extremités du tableau
				$tableau_php = calcul_tableau_grille($tableau[$j]);
			
				//calcul erreur
				if ($GLOBALS["bouton_envoi"] == '') $erreur='';
				else {
					list($nbr_erreurs, $nbr_vides) = comparaison($tableau_php); 
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
		if ($j!=0 and $j!=count($tableau)-1)	//pas les extremités du tableau
				$tableau[$j] = code_echappement(_GRILLE_
					.'<div class="spip horizontal"><h4 class="spip grille">'
					._T('motscroises:horizontalement')." :</h4>\n"
					.mot_croises_listes(trim($i))."</div>"._GRILLE_);
		$j++;
	}
	$texte = implode($tableau);
	
	// definitions verticales
	$tableau = preg_split("/<vertical>|<\/vertical>/",$texte);
	$j = 0;
	foreach ($tableau as $i){
		if ($j!=0 and $j!=count($tableau)-1)	//pas les extremités du tableau
				$tableau[$j] = code_echappement(_GRILLE_
					.'<div class="spip vertical"><h4 class="spip grille">'
					._T('motscroises:verticalement')." :</h4>\n"
					.mot_croises_listes(trim($i))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>"._GRILLE_); // Bug IE ?
		$j++;
	}
	$texte = implode($tableau);
	
	// solution
	if ($GLOBALS["solution"][0] == 1) $texte .= code_echappement(_GRILLE_.affichage_grille($tableau_php, true)._GRILLE_);

	return $texte;
}

function mot_croises_header_prive($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('mots-croises-prive.css')).'" />';
	$flux .='<script type="text/javascript" src="'.find_in_path("mots-croises.js").'"></script>';
	return $flux;
}

function mot_croises_insert_head($flux){
	return $flux."<link rel=\"stylesheet\" type=\"text/css\" href=\"".direction_css(find_in_path("mots-croises.css"))."\" />\n<script src=\"".find_in_path("mots-croises.js")."\" type=\"text/javascript\"></script>";
}
?>