<?php
#---- filtres mot-croisés ----------#
#Filtre : grille de mot croisés		#
#Auteur : Maïeul Rouquette,2006		#
#Licence : GPL				#
#Contact : maieulrouquette@tele2.fr	#

function lettre($texte) //return la lettre correpondant au chiffres
{$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
 return $alphabet[$texte-1];}


              
function solution_grille($tableau_grille){//cree une grille affichant la solution
    
	$hauteur =sizeof($tableau_grille);
    $largeur= sizeof($tableau_grille[0]);
    
    $grille='<table class=\"grille\" summary=\"'._T('grille:table_summary',Array('hauteur'=>$hauteur,'largeur'=>$largeur))."\">\n\t<tr>\n\t\t<td class=\"coin\"></td>\n";
    
    $increment_largeur=1;   //un icnrement pour les cellues d'entete
    
   
    while ($increment_largeur<=$largeur){
        $grille=$grille."\t\t<th scope=\"col\">".$increment_largeur."</th>\n";
        $increment_largeur++;}
    
    $grille=$grille."\t</tr>\n";
    
        
    
    // création des lignes
    foreach($tableau_grille as $ligne =>$contenu_ligne){
        $ligne=$ligne+1;
        $grille=$grille."\t<tr>\n\t\t<th scope=\"row\">".lettre($ligne)."</th>\n";//entête de ligne
        
        foreach ($contenu_ligne as $cellule){
            if ($cellule=="*"){
                    $grille=$grille."\t\t<td class=\"noir\">*</td>\n";
                }
            else{$grille=$grille."\t\t<td>".$cellule."</td>\n";}
                            }
        }
        $grille=$grille."\t</tr>\n";
        
    $grille=$grille."</table>\n";
    return $grille;
    }  
function grille_formulaire($tableau_grille){
	$page=self();
    $hauteur =sizeof($tableau_grille);
    $largeur= sizeof($tableau_grille[0]);
  	
    $grille="<form action=\"".$page."\" method=\"post\">\n"; // début formulaire
    
   	$grille=$grille."<table class=\"grille\" summary=\""._T('grille:table_summary',Array('hauteur'=>$hauteur,'largeur'=>$largeur))."\">\n\t<tr>\n\t\t<td class=\"coin\"></td>\n";// debut tableau + 1ere celule
    $increment_largeur=1;   //un iccrément pour les cellules d'entete
    while ($increment_largeur<=$largeur){
        $grille=$grille."\t\t<th scope=\"col\">".$increment_largeur."</th>\n";
        $increment_largeur++;}
    
    $grille=$grille."\t</tr>\n";
    
    foreach($tableau_grille as $ligne =>$contenu_ligne){
        $ligne++;
        $grille=$grille."\t<tr>\n\t<th scope=\"row\">".lettre($ligne)."</th>\n";//numeros de ligne
        
        foreach ($contenu_ligne as $colonne =>$cellule){
            $colonne++;
            //s'il s'agit d'un noir
            if ($cellule=="*"){
                    $grille=$grille."\t\t<td class=\"noir\">*</td>\n";
                                }
            else {  $grille=$grille."\t\t<td>";
            		
            		$grille=$grille.'<label for="col'.$colonne.'lig'.$ligne.'">'._T('grille:ligne',Array('n'=>lettre($ligne))).';'._T('grille:colonne',Array('n'=>$colonne)).'</label>';
                    
                    if (isset($GLOBALS['col'.$colonne.'lig'.$ligne])) //: test l'existence de la variable global correpsonte à cette cellule
                        {
                        
                        $grille=$grille.'<input type="text" maxlength="1" value="'.$GLOBALS['col'.$colonne.'lig'.$ligne].'" name="col'.$colonne.'lig'.$ligne.'" id="col'.$colonne.'lig'.$ligne."\" /></td>\n";
                        }
                    else
                        {
                        
                        $grille=$grille.'<input type="text" maxlength="1"  name="col'.$colonne.'lig'.$ligne.'" id="col'.$colonne.'lig'.$ligne."\" /></td>\n";                        
						}
            
				}
		}
                                                    
        $grille=$grille."\t</tr>\n";}
        
    
    $grille=$grille."</table>\n";
    $grille=$grille."<br /><input id=\"solution\" name=\"solution[]\" type=\"checkbox\"value=\"1\" /><label for=\"solution\" >"._T('grille:afficher_solution')."</label><br />\n
<input type=\"submit\" value=\""._T('grille:verifier')."\" name=\"bouton_envoi\" /></form>\n";

    return  $grille;}

function grille($texte,$page=''){						//on garde $page pour compatibilité
	if (eregi("<grille>",$texte)!=1){return $texte;} //verifie s'il y a une grille de mot croisé
    
    	include_spip('inc/calculer_grille');
	$tableau_grille=calcul_tableau_grille($texte);
    
   
    
	$grille_formulaire=grille_formulaire($tableau_grille);
	
	if ($GLOBALS["bouton_envoi"] == ''){$erreur='';}
	else {
		include_spip('inc/verification');
		
    		$nbr_erreur=comparaison($tableau_grille); 
    		
		$erreur='<strong class="erreur">';
		if ($nbr_erreur==0){$erreur.= _T('grille:aucune_erreur');}
    		if ($nbr_erreur>=2){$erreur.= _T("grille:nombre_erreurs", Array('nbr'=>$nbr_erreur));}
    		if ($nbr_erreur==1){$erreur.= _T('grille:1erreur');}
		$erreur.='</strong>';
		}
       
                              
	$texte=eregi_replace('<p class="spip"><grille></p>.*</grille></p>',$erreur.$grille_formulaire,$texte);
    // les defs verticals
    $texte=eregi_replace('<p class="spip"><vertical></p>','<div class="spip vertical"><h4 class="spip grille">'._T('grille:verticalement').' :</h4>',$texte);
   	$texte=eregi_replace("</vertical></li></ol>",'</li></ol></div>',$texte);
    
    // les definitions horizontal
    $texte=eregi_replace('<horizontal></p>','<div class="spip horizontal"><h4 class="spip grille">'._T('grille:horizontalement').' :</h4>',$texte);
    $texte=eregi_replace("</horizontal></li></ol>",'</li></ol></div>',$texte);
   
   	if ($GLOBALS["solution"][0] == 1){$texte = $texte."<div class=\"solution\"><h2 class=\"spip\">"._T('grille:Solution')." : </h2>".solution_grille($tableau_grille)."</div>";}
     
    
    return $texte;
    } 
#--- fin filtre mot-croisés ---#

?>