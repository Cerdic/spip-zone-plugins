<?php

function mot_croises_pre_propre($texte){
	
	
	if (! preg_match("/<grille>|<\/grille>/",$texte))
		{return $texte;}
	$echappeur = "<!--grille-->";		//pour pas_de_grille
	
	
	include_spip('inc/calculer_grille');
	
	$tableau = preg_split("/<grille>|<\/grille>/",$texte);					//sera uniquement le tableau spip, mais on attend pour le moment
	$j =0;
	
	foreach ($tableau as $i){
			if ($j!=0 and $j!=count($tableau)-1)	//pas les extremités du tableau
				{
				include_spip('inc/affichage_grille');
				$tableau_php = calcul_tableau_grille($tableau[$j]);
			
				//calcul erreur
				if ($GLOBALS["bouton_envoi"] == ''){$erreur='';}
				else {
					include_spip('inc/verification');
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
/*
				if ($GLOBALS["bouton_envoi"] == ''){$erreur='';}
				else {
					include_spip('inc/verification');
					
						$nbr_erreur=comparaison($tableau_php); 
						
					$erreur='<strong class="erreur">';
					if ($nbr_erreur==0){$erreur.= _T('motscroises:aucune_erreur');}
						if ($nbr_erreur>=2){$erreur.= _T("motscroises:nombre_erreurs", Array('nbr'=>$nbr_erreur));}
						if ($nbr_erreur==1){$erreur.= _T('motscroises:1erreur');}
					$erreur.='</strong>';
					}
*/					
				//fin calcul erreur
				
				$tableau[$j] = code_echappement($echappeur.$erreur.affichage_grille($tableau_php).$echappeur);	
				
						
				}
			
			
			$j++;
			}
		
		
	
	
	
	include_spip('inc/affichage_grille');
	
	$texte = implode($tableau);
	
	//debut def horizontalement
	$tableau = preg_split("/<horizontal>|<\/horizontal>/",$texte);
	$j =0;
	
	foreach ($tableau as $i){
		
		if ($j!=0 and $j!=count($tableau)-1)	//pas les extremités du tableau
				{
				
				$tableau[$j] = code_echappement($echappeur."<div class=\"spip horizontal\">\n
				<h4 class=\"spip grille\">"._T('motscroises:horizontalement')." :</h4>\n".liste_spip(trim($i))."</div>".$echappeur);
			
				
			}
	$j++;
	}
	$texte = implode($tableau);
	
	
	
	//fin def horizontal et debut-def-vertical
	
	$tableau = preg_split("/<vertical>|<\/vertical>/",$texte);
	$j =0;
	
	foreach ($tableau as $i){
		
		if ($j!=0 and $j!=count($tableau)-1)	//pas les extremités du tableau
				{
				
				$tableau[$j] = code_echappement($echappeur."<div class=\"spip vertical\">\n
				<h4 class=\"spip grille\">"._T('motscroises:verticalement')." :</h4>\n".liste_spip(trim($i))."</div>".$echappeur);
			
				
			}
	$j++;
	}
	$texte = implode($tableau);
	
	//fin def-vertical
	
	if ($GLOBALS["solution"][0] == 1){$texte = $texte.code_echappement($echappeur.affichage_grille($tableau_php,true).$echappeur);} //solution

	return $texte;
	
	
}

function liste_spip ($texte){				//transforme les listes verticales/horizontale spip en html
	
	$texte = preg_replace('/-#/','<li>',$texte);
	
	$texte = preg_replace("/\n/",'</li>',$texte);
	
	
	return '<ol>'.$texte."</li>\n</ol>";}


?>