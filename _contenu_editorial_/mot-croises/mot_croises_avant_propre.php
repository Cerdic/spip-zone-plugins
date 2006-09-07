<?php

function mot_croises_pre_propre($texte){
	
	$echappeur_debut = '<html><!-- grille--></html>';		//pour pas_de_grille
	$echappeur_fin = '<html><!-- fin-grille--></html>';	//pour pas_de_grille
	
	include_spip('inc/calculer_grille');
	
	$tableau = preg_split("/<grille>|<\/grille>/",$texte);					//sera uniquement le tableau spip, mais on attend pour le moment
	$j =0;
	
	foreach ($tableau as $i){
			if ($j!=0 and $j!=count($tableau)-1)	//pas les extremités du tableau
				{
				include_spip('inc/affichage_grille');
				$tableau_php = calcul_tableau_grille2($tableau[$j]);
				
				//calcul erreur
				if ($GLOBALS["bouton_envoi"] == ''){$erreur='';}
				else {
					include_spip('inc/verification');
					
						$nbr_erreur=comparaison($tableau_php); 
						
					$erreur='<strong class="erreur">';
					if ($nbr_erreur==0){$erreur.= _T('grille:aucune_erreur');}
						if ($nbr_erreur>=2){$erreur.= _T("grille:nombre_erreurs", Array('nbr'=>$nbr_erreur));}
						if ($nbr_erreur==1){$erreur.= _T('grille:1erreur');}
					$erreur.='</strong>';
					}
				//fin calcul erreur
				
				$tableau[$j] = $echappeur_debut.$erreur.affichage_grille($tableau_php).$echappeur_fin;				
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
				
				$tableau[$j] = $echappeur_debut."<div class=\"spip horizontal\">\n
				<h4 class=\"spip grille\">"._T('grille:horizontalement')." :</h4>\n".liste_spip(trim($i))."</div>".$echappeur_fin;
			
				
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
				
				$tableau[$j] = $echappeur_debut."<div class=\"spip vertical\">\n
				<h4 class=\"spip grille\">"._T('grille:verticalement')." :</h4>\n".liste_spip(trim($i))."</div>".$echappeur_fin;
			
				
			}
	$j++;
	}
	$texte = implode($tableau);
	
	//fin def-vertical
	
	if ($GLOBALS["solution"][0] == 1){$texte = $texte.$echappeur_debut.affichage_grille($tableau_php,true).$echappeur_fin;} //solution

	
	
	return $texte ;
}

function liste_spip ($texte){				//transforme les listes verticales/horizontale spip en html
	
	$texte = preg_replace('/-#/','<li>',$texte);
	
	$texte = preg_replace("/\n/",'</li>',$texte);
	
	
	return '<ol>'.$texte."</li>\n</ol>";}


?>