<?php
/*
 * Nom du filtre      : QCM
 * Auteur             : (Mathieu GIANNECCHINI) 
 						amélioré et adapté par Patrice VANNEUFVILLE en plugin
 * Date               : (7 Août 2003) 30 octobre 2006
 * Fonction du filtre : Parse le code du QCM tapé dans SPIP et stocke
 *                      les questions, réponses et commentaires
 *                      dans un tableau et retourne le code HTML du QCM
 */
function qcm($chaine){

  // Messages utilisés dans le QCM
  $titreQCM="QCM";
  $reponseJuste="La r&eacute;ponse est juste";
  $reponseFausse="La r&eacute;ponse est fausse";
  $reponseNulle="Vous n'avez pas r&eacute;pondu";
  $reinitialiser='R&eacute;initialiser le QCM';
  $corriger='Corriger';
  $codeDebut='<qcm>';
  $codeFin='</qcm>';
  
  // Bascule permettant de gérer les points spécifiés dans le  code du QCM
  $gestionPoint=true;

  // Booléen permettant de savoir si il y a un QCM dans l'article
  $qcm=false;
 
  // Initialisation du score
  $score=0;

  // On récupère le code du qcm entre <qcm> et </qcm>
  while(ereg($codeDebut,$chaine)){
  $qcm=true;
  $codeHTML='';
  list($texteAvant,$suite)=explode($codeDebut,$chaine,2); 
  list($qcm,$texteApres)=explode($codeFin,$suite,2); 
 
  // On isole les questions les réponses et les commentaires
  $ligne=split("\n",$qcm);
  $nbligne=count($ligne);

  $premierequestion=$nbQuestion;
     
  for($i=0;$i<$nbligne;$i++){
    // Pour chaque ligne on regarde le premier caractère
	$li=trim($ligne[$i]); 
    switch($li[0]){
      case 'T' : 
	  	// On extrait le titre
	  	$titreQCM=substr($li,1);break;

      case 'Q' : 
	  	// On extrait les questions
		$nbQuestion++;
		$question[$nbQuestion]=substr($li,1);
        break;

      case 'P' : 
	  	// On extrait les précisions de la proposition
	 	list($reponse,$precis)=explode("|",$li);
	
		// On extrait le numéro de la proposition et son contenu
		ereg("^P([0-9]+)(.*)",$reponse,$eregResult1);	
		$numPropos=$eregResult1[1];

        // On extrait les points éventuellement associés à chaque réponse
        if(ereg("^.(-?[0-9]+)(.*)",$eregResult1[2],$eregResult2)){
          $points[$nbQuestion][$numPropos]=$eregResult2[1];
          $choix[$nbQuestion][$numPropos]= $eregResult2[2];
          $gestionPoint=true;
        }
        // Si une erreur de syntaxe apparait on passe en mode par défaut pour la gestion des points
		else {
//          $gestionPoint=false;
          $points[$nbQuestion][$numPropos]=1;
          $choix[$nbQuestion][$numPropos]= $eregResult1[2];
        }        
       	$precision[$nbQuestion][$numPropos]= $precis;
		break;
	
      case 'R' :
		// On récupère le numéro de la bonne réponse
		ereg("^R([0-9]+)",$li,$eregResult);	
		$numProposition=$eregResult[1];
		$bonneReponse[$nbQuestion]=$numProposition;
		break;

      default  : break;
    }
  }	
  
  /******************** 
   * Affichage du QCM *
   ********************/ 
  // Si on ne corrige pas on affiche le QCM
  if (!isset($GLOBALS["var_correction"])){
    for($i=$premierequestion+1;$i<=count($question);$i++){
     $nomVarSelect="var_Q".$i;
     $codeHTML.='<div class="spip_qcm_question">'.definir_puce().$question[$i].'</div>';
     $codeHTML.='<div class="spip_qcm_proposition">';

      // Si il ya plus de 5 choix, on utilise une liste
      if(count($choix[$i])>5){
        $codeHTML.="<select name=\"$nomVarSelect\" class=\"spip_qcm_select\">";
		for($j=1;$j<=count($choix[$i]);$j++)
			$codeHTML.="<option value=\"$j\">".$choix[$i][$j].'</option>';
		$codeHTML.="</select>";
      }
      // Sinon des radio boutons
      else{
        for($j=1;$j<=count($choix[$i]);$j++){
          $codeHTML.="<input 
              type=\"radio\" 
              name=\"$nomVarSelect\"
			  value=\"$j\" id=\"$nomVarSelect.$j\"><label for=\"$nomVarSelect.$j\">";
          $codeHTML.=$choix[$i][$j]."</label><br />";
//              value=\"$j\">";
//	  $codeHTML.=$choix[$i][$j]."<br />";
         }
       }
       $codeHTML.="</div><br />";
       
     }	// Fin traitement des questions
    }	//Fin du cas sans correction

    // Sinon on affiche la correction
    else{
     
      // Initialisation du code à retourner
      $codeHTML='';
 
      for($i=$premierequestion+1;$i<=count($question);$i++){
        $nomVarSelect="var_Q".$i;
        
        // On comptabilise le maximum de points que l'on peut obtenir
        if($gestionPoint){
          $pointParQuestion=$points[$i][$bonneReponse[$i]];
          $scoreMax+=$pointParQuestion;
        }
        else{
          $pointParQuestion=1;
          $scoreMax+=$pointParQuestion;
        }
       
        // La réponse choisie
        $codeHTML.='<div class="spip_qcm_question">'.definir_puce().$question[$i].'<span class="spip_qcm_point">&nbsp;('.$pointParQuestion.' pt)</span></div>';

        // Si c'est juste
        if($bonneReponse[$i]==$GLOBALS[$nomVarSelect]){
           // Les points par défaut : 1 par bonne réponse
           if($gestionPoint)
		      $score+=$points[$i][$GLOBALS[$nomVarSelect]];
           	  else $score++;

           $codeHTML.='<div class="spip_qcm_reponse">'.$introReponse
           		 .$choix[$i][$GLOBALS[$nomVarSelect]].'</div>'
           		 .'<div class="spip_qcm_correction_juste">'._T('qcm:reponseFausse').'</div>';
           if (isset($precision[$i][$GLOBALS[$nomVarSelect]]))
             $codeHTML.='<div align="center"><div class="spip_qcm_precision">'
				 .$precision[$i][$GLOBALS[$nomVarSelect]]
	     	 	 .'</div></div>';
           
        }
        // Si c'est faux
        else{
          if(isset($GLOBALS[$nomVarSelect])){
           $score+=$points[$i][$GLOBALS[$nomVarSelect]];
           $codeHTML.='<div class="spip_qcm_reponse">'.$choix[$i][$GLOBALS[$nomVarSelect]].'</div>'
           			  .'<div class="spip_qcm_correction_faux">'._T('qcm:reponseFausse').'</div>';
           
           if (isset($precision[$i][$GLOBALS[$nomVarSelect]]))
             $codeHTML.='<div align="center"><div class="spip_qcm_precision">'
	     				.$precision[$i][$GLOBALS[$nomVarSelect]].'</div></div>';
          }
	  	  else $codeHTML.='<div class="spip_qcm_correction_null">'.$reponseNulle.'</div>';
        }
        $codeHTML.='<br />';
      }
     
   }
   // On ajoute les balises <html> et </html>
   // $codeHTML='<html>'.$codeHTML.'</html>';
   
   $chaine=$texteAvant.$codeHTML.$texteApres;
  }//fin du while

  if($qcm){
    if (!isset($GLOBALS['var_correction'])){
       $teteFormulaire='<form method="post" action="">';  
       $piedFormulaire='
       <br>
       <input type="hidden" name="var_correction" value="yes">
       <div align="center"><input type="submit" value="'.$corriger.'" class="spip_qcm_bouton_corriger"></div>
       </form>';
       $chaine=$teteFormulaire.$chaine.$piedFormulaire;
    }
    else{
      // On ajoute le score final
      $chaine.="<center><div class=\"spip_qcm_score\">Score&nbsp;:&nbsp;$score&nbsp;/&nbsp;$scoreMax<br></div></center>"
		  	   .'<div class="spip_qcm_bouton_corriger" align="right">[ <a href="'
	  		   .parametre_url(self(),'var_mode','recalcul').'">'.$reinitialiser.'</a> ]</div>';
      unset($GLOBALS["var_correction"]);
    }

     $chaine='<div class="spip_qcm"><div class="spip_qcm_titre">'.$titreQCM.'<hr /></div>'.$chaine.'</div>';
  }
   return $chaine;
}


function qcm_stylesheet_public($b) {
 return '<link rel="stylesheet" href="'.find_in_path($b)."\" type=\"text/css\" media=\"projection, screen\" />\n";
}
function qcm_stylesheet_prive($b) {
 return '<link rel="stylesheet" href="'._DIR_PLUGIN_QCM."$b\" type=\"text/css\" media=\"projection, screen\" />\n";
}

function qcm_header_prive($flux){
	return $flux . qcm_stylesheet_prive('qcm.css');
}

function qcm_insert_head($flux){
	return $flux 
	. "<!-- CSS QCM -->\n"
	. qcm_stylesheet_public('qcm.css');
}

function qcm_pre_typo($texte) {
	return qcm($texte);
}	

?>