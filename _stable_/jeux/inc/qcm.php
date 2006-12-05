<?php
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
/*

 Insere un QCM dans vos articles !
---------------------------------------
 Idee originale de Mathieu GIANNECCHINI
---------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : [qcm] // TODO : le titre optionnel...
separateurs optionnels   : [titre], [texte]

Exemple de syntaxe dans l'article :
------------------------------------

<jeux>
	[titre]
	Un titre pour le QCM !

	[qcm]
	Q Une question bla bla
	P1 Une proposition 1
	P2 Une proposition 2
	P3 Une proposition 3 (la bonne réponse)
	R3 (la réponse 3 est la bonne !)

	[qcm]
	Q Une question encore
	P1.2 Une proposition à 2 points (la bonne réponse)
	P2 Une proposition 2
	R1

	[qcm]
	Q Une question pour finir
	P1 Une proposition 1|Un commentaire 1
	P2 Une proposition 2|Un commentaire 2
	P3.4 Une proposition à 4 points !|Effectivement !
	P4 Une proposition 4|Un commentaire 4
	P5 Une proposition 5|Un commentaire 5
	P6 Une proposition 6|Un commentaire 6
	R3
</jeux>

*/

/* à supprimer à terme...

  Nom du plugin      : 	Un QCM dans vos articles
  Auteur             : 	Patrice VANNEUFVILLE 
 						(sur une idee originale de Mathieu GIANNECCHINI, 2003) 
  Date               : 	30 octobre 2006
  Fonction du plugin : 	Parse le code du QCM tape dans SPIP et stocke
                       	les questions, reponses et commentaires
                       	dans un tableau et retourne le code HTML du QCM
  
*/
 
 
// cette fonction remplit le tableau $qcms sur la question $indexQCM
function qcm_analyse_le_qcm($qcm, $indexQCM) {
  global $qcms;
  $lignes = split("\n", $qcm);
  foreach ($lignes as $ligne) {
	$li=trim($ligne); 
    switch($li[0]){
      case 'Q' : 
	  	// On extrait la question
		$qcms[$indexQCM]['question'] = trim(substr($li, 1));
		$qcms[$indexQCM]['maxscore'] = 0;
		$qcms['nbquestions']++;
        break;

      case 'P' : 
	  	// On extrait une proposition
		
	  	// On extrait les precisions de la proposition
	 	list($reponse, $precision)=explode("|", $li);
	
		// On extrait le numero de la proposition et son contenu
		ereg("^P([0-9]+)(.*)", $reponse, $eregResult);	
		$indexProposition = $eregResult[1];
		$suiteProposition = trim($eregResult[2]);
		$qcms[$indexQCM]['nbpropositions']++;

        // On extrait les points eventuellement associes a chaque reponse
        if(ereg("^\.(-?[0-9]+)(.*)", $suiteProposition, $eregResult)){
          $qcms[$indexQCM]['points'][$indexProposition] = intval($eregResult[1]);
          $qcms[$indexQCM]['propositions'][$indexProposition] = trim($eregResult[2]);
        }
		else {
          $qcms[$indexQCM]['points'][$indexProposition] = 0;
          $qcms[$indexQCM]['propositions'][$indexProposition] = $suiteProposition;
        }     
		
		// la precision eventuelle...
       	$qcms[$indexQCM]['precisions'][$indexProposition] = trim($precision);
		break;
	
      case 'R' :
		// On recupere le numero et les points de la bonne reponse
		ereg("^R([0-9]+)", $li, $eregResult);	
		$qcms[$indexQCM]['bonnereponse'] = $eregResult[1];

		// au cas ou les points ne sont pas specifies pour la bonne reponse
		if ($qcms[$indexQCM]['points'][$eregResult[1]]==0) 
		  $qcms[$indexQCM]['points'][$eregResult[1]] = 1;

		// total des points des bonnes reponses
		$qcms[$indexQCM]['maxscore'] = $qcms[$indexQCM]['points'][$eregResult[1]];
		break;

      default : break;
    }
  } // foreach
} // function

// cette fonction retourne le texte entre deux balises si elles sont presentes
// et false dans le cas contraire
function qcm_recupere_le_titre(&$chaine, $ouvrant, $fermant) {
  // si les balises ouvrantes et fermantes ne sont pas presentes, c'est mort
  if (strpos($chaine, $ouvrant)===false || strpos($chaine, $fermant)===false) return false;
  list($texteAvant, $suite) = explode($ouvrant, $chaine, 2); 
  list($texte, $texteApres) = explode($fermant, $suite, 2); 
  // on supprime les balises de l'affichage...
  $chaine = $texteAvant.jeux_rem('QCM-DEBUT', 0).$texteApres;
  return trim($texte);
}

function qcm_les_points($phrase, $points) {
    $pointsHTML = '<span class="jeux_point"> ('.$points. _T('jeux:point'.($points>1?'s':'')).')</span>';
  	if (ereg('^(.*)( ?:)( *)$', $phrase, $eregResult)) $phrase = $eregResult[1].$pointsHTML.$eregResult[2].$eregResult[3];
	  else $phrase .= $pointsHTML;
	return $phrase;  
}

function qcm_affiche_la_question($indexQCM, $corrigee, $gestionPoints) {
  global $qcms, $qcm_score;
  if ($qcms[$indexQCM]['propositions']==0) return '';

  // Initialisation du code a retourner
  $nomVarSelect = 'var_Q'.$indexQCM;
  $question = trim(str_replace('&nbsp;', ' ', $qcms[$indexQCM]['question']));
  
  // affichage des points dans la question
  if ($gestionPoints) {
    $pointsQ = $qcms[$indexQCM]['maxscore'];
	$question = qcm_les_points($question, $pointsQ);
  } else $pointsQ = 1;

  $codeHTML = "<div class=\"jeux_question\">".definir_puce().$question.'</div>';
  if (!$corrigee){
    // affichage sans correction :
     $codeHTML.="\n<div class=\"qcm_proposition\">";

      // Si il ya plus de 5 choix, on utilise une liste
      if(count($qcms[$indexQCM]['propositions'])>5){
        $codeHTML.='<select name="'.$nomVarSelect.'" class="qcm_select">';
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) $codeHTML.="<option value=\"$i\">$valeur</option>";
		$codeHTML.='</select>';
      }
      // Sinon des radio boutons
      else {
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) 
          $codeHTML.='<input type="radio" class="qcm_radio" name="'.$nomVarSelect
		  	. '" value="'.$i.'" id="'.$nomVarSelect.$i.'"><label for="'.$nomVarSelect.$i.'">'
          	. $valeur.'</label><br />';
       }
       $codeHTML.="</div><br />";
       
    }	// fin du cas sans correction

  // Sinon on affiche la correction
  else {
 
	 if ($_POST[$nomVarSelect]) {
		// les points de la reponse donnee...
		$pointsR = $qcms[$indexQCM]['points'][$_POST[$nomVarSelect]];
		
		// la reponse donnee & precision des points eventuels de la mauvaise reponse...
		$codeHTML.='<div class="qcm_reponse">'
			 .((($pointsR==$pointsQ) || ($pointsR==0))?_T('jeux:votre_choix'):qcm_les_points(_T('jeux:votre_choix'), $pointsR))
			 .$qcms[$indexQCM]['propositions'][$_POST[$nomVarSelect]].'</div>';

		// on donne les points de la reponse quoiqu'il arrive
		$qcm_score += $pointsR;
		
        if ($qcms[$indexQCM]['bonnereponse']==$_POST[$nomVarSelect]) 
        // Si c'est juste
			$codeHTML .= '<div class="qcm_correction_juste">'._T('qcm:qcm_reponseJuste').'</div>';
        // Si c'est faux
         else $codeHTML .= '<div class="qcm_correction_faux">'._T('qcm:qcm_reponseFausse').'</div>';
           
        // les precisions eventuelles
        if ($qcms[$indexQCM]['precisions'][$_POST[$nomVarSelect]]<>"")
             $codeHTML.='<div align="center"><div class="qcm_precision">'
				 .$qcms[$indexQCM]['precisions'][$_POST[$nomVarSelect]]
	     	 	 .'</div></div>';

	// pas de reponse postee...
	} else $codeHTML.='<div class="qcm_correction_null">'._T('qcm:qcm_reponseNulle').'</div>';
	   
	$codeHTML.='<br />';
     
  } // fin du cas avec correction
  return $codeHTML;
}

function qcm_inserer_les_qcm(&$chaine, $indexJeux, $gestionPoints) {
  global $qcms;
  if (ereg('<ATTENTE_QCM>([0-9]+)</ATTENTE_QCM>', $chaine, $eregResult)) {
	$indexQCM = intval($eregResult[1]);
	list($texteAvant, $texteApres) = explode($eregResult[0], $chaine, 2); 
	$chaine = $texteAvant.jeux_rem('QCM-DEBUT', $indexQCM)
		. qcm_affiche_la_question($indexQCM, isset($_POST["var_correction_".$indexJeux]), $gestionPoints)
		. jeux_rem('QCM-FIN', $indexQCM)
		. qcm_inserer_les_qcm($texteApres, $indexJeux, $gestionPoints); 
  }
  return $chaine;
}

function jeux_qcm($texte, $indexJeux) {

  // initialisation  
  global $qcms, $qcm_score;
  $indexQCM = $qcm_score = 0;
  $qcms['nbquestions'] = $qcms['totalscore'] = $qcms['totalpropositions'] = 0;
	  $horizontal = $vertical = $solution = $html = false;
  $titre = _T('qcm:qcm_titre');

  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('qcm', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_QCM) {
		// remplacement des qcm par : <ATTENTE_QCM>ii</ATTENTE_QCM>
		$html .= "<ATTENTE_QCM>$indexQCM</ATTENTE_QCM>";
		// On analyse le QCM
		qcm_analyse_le_qcm($tableau[$i+1], $indexQCM);
	    $qcms['totalpropositions'] +=  count($qcms[$indexQCM]['propositions']);
    	$qcms['totalscore'] +=  $qcms[$indexQCM]['maxscore'];
	  	$indexQCM++;
	  }
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
  }

  // est-ce certaines questions ne valent pas 1 point ?
  $gestionPoints = $qcms['totalscore']<>$qcms['nbquestions'];

  // reinserer les qcms mis en forme
  $texte = qcm_inserer_les_qcm($html, $indexJeux, $gestionPoints);

  // calcul des extremes
  $tete = '<div class="jeux qcm"><div class="jeux_titre">'.$titre.'<hr /></div>';
  if (!isset($_POST["var_correction_".$indexJeux])) { 
	$tete .= "\n".'<form method="post" action="">';
	$pied = '<br>
	<input type="hidden" name="var_correction_'.$indexJeux.'" value="yes">
	<div align="center"><input type="submit" value="'._T('jeux:corriger').'" class="jeux_bouton_corriger"></div>
	</form>';
  } else {
      $pied = jeux_afficher_score($qcm_score, $qcms['totalscore'])
  			. jeux_bouton_reinitialiser();
  }
  
  unset($qcms); unset($qcm_score);
  return $tete.$texte.$pied;
}

?>