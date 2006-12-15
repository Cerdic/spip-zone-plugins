<?php
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice�.!vanneufville�@!laposte�.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
/*

 Insere un QCM dans vos articles !
----------------------------------
 Idee originale de Mathieu GIANNECCHINI
---------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : [qcm], oui [quiz]
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
	P3 Une proposition 3 (la bonne r�ponse)
	R3 (la r�ponse 3 est la bonne !)
	[qcm]
	Q Une question encore
	P1.2 Une proposition � 2 points (la bonne r�ponse)
	P2 Une proposition 2
	R1
	[qcm]
	Q Une question pour finir
	P1 Une proposition 1|Un commentaire 1
	P2 Une proposition 2|Un commentaire 2
	P3.4 Une proposition � 4 points !|Effectivement !
	P4 Une proposition 4|Un commentaire 4
	P5 Une proposition 5|Un commentaire 5
	P6 Une proposition 6|Un commentaire 6
	R3
</jeux>

Cas particulier : lorsque l'utilisateur doit lui-meme taper la reponse :
<jeux>
[qcm]
Q Citez une planete du systeme solaire :
P mercure venus terre mars jupiter saturne uranus neptune
</jeux>

La liste des mots a placer apres "P" peut accepter 
les separateurs usuels : 
	retours � la ligne, tabulations, espaces
	virgules, point-virgules, points
Pour une expression comprenant des espaces, utiliser les 
guillemets ou le signe + :
	par ex. : "afin de" est equivalent a : afin+de
Les minuscules ou majuscules peuvent etre utilisees indifferemment.
La gestion des points et des pr�cisions est toujours possible :
	P.4 mercure venus terre mars jupiter saturne uranus neptune|saviez-vous que pluton n'est plus une planete ?

*/


// cette fonction remplit le tableau $qcms sur la question $indexQCM
function qcm_analyse_le_qcm($qcm, $indexQCM) {
  global $qcms;
  $lignes = preg_split("/\r?\n/", $qcm);
  foreach ($lignes as $ligne) {
	$li=trim($ligne); 
    switch($li[0]){
      case 'Q' : 	  	// On extrait la question
		$qcms[$indexQCM]['question'] = trim(substr($li, 1));
		$qcms[$indexQCM]['maxscore'] = 0;
		$qcms['nbquestions']++;
        break;
      case 'P' : 	  	// On extrait une proposition
	  	// Pour les precisions de la proposition...
	 	list($reponse, $precision)=explode("|", $li);
		// On extrait le numero de la proposition et son contenu
		ereg("^P([0-9]*)(.*)", $reponse, $eregResult);	
		$indexProposition = intval($eregResult[1]);
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
		// cas d'un trou (ou d'une proposition non numerotee !)
		if ($indexProposition==0) {
			$qcms[$indexQCM]['maxscore'] = $qcms[$indexQCM]['points'][0] = max($qcms[$indexQCM]['points'][0], 1);
			$qcms[$indexQCM]['propositions'] = jeux_liste_mots_min($qcms[$indexQCM]['propositions'][0]);
			$qcms[$indexQCM]['nbpropositions'] = 1;
		}
		break;
      case 'R' :		// On recupere le numero et les points de la bonne reponse
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

function qcm_les_points($phrase, $points) {
    $pointsHTML = '<span class="jeux_point"> ('.$points. _T('jeux:point'.($points>1?'s':'')).')</span>';
  	if (ereg('^(.*)( ?:)( *)$', $phrase, $eregResult)) $phrase = $eregResult[1].$pointsHTML.$eregResult[2].$eregResult[3];
	  else $phrase .= $pointsHTML;
	return $phrase;  
}

function qcm_afficher_le_trou($nomVarSelect, $indexQCM) {
  global $qcms;
  $sizeInput = 0;
  foreach($qcms[$indexQCM]['propositions'] as $mot) $sizeInput = max($sizeInput, strlen($mot));
  $prop = strtolower($_POST[$nomVarSelect] = trim($_POST[$nomVarSelect]));
  return " <input name=\"$nomVarSelect\" class=\"jeux_input\" size=\"$sizeInput\" type=\"text\"> ";
}

function qcm_affiche_la_question($indexJeux, $indexQCM, $corrigee, $gestionPoints) {
  global $qcms, $qcm_score;
  if (!$qcms[$indexQCM]['nbpropositions'] || !$qcms[$indexQCM]['maxscore']) 
  	return "<div class=\"jeux_question\">".definir_puce()._T('jeux:erreur_syntaxe').'</div><br />';
//print_r($qcms[$indexQCM]);
  // Initialisation du code a retourner
  $nomVarSelect = "var{$indexJeux}_Q{$indexQCM}";
  $question = trim(str_replace('&nbsp;', ' ', $qcms[$indexQCM]['question']));
  $trou = $qcms[$indexQCM]['nbpropositions']==1;
  
  // affichage des points dans la question
  if ($gestionPoints) {
    $pointsQ = $qcms[$indexQCM]['maxscore'];
	$question = qcm_les_points($question, $pointsQ);
  } else $pointsQ = 1;

  $codeHTML = "<div class=\"jeux_question\">".definir_puce().$question.'</div>';
  if (!$corrigee){
    // affichage sans correction :
     $codeHTML.="\n<div class=\"qcm_proposition\">";

      // S'il n'y a qu'1 seul choix, on affiche un trou
      // S'il y a plus de 5 choix, on utilise une liste
      // Sinon, entre 2 et 4 choix, des radio boutons
	  if ($trou) {
        $codeHTML.=qcm_afficher_le_trou($nomVarSelect, $indexQCM);
      } elseif ($qcms[$indexQCM]['nbpropositions']>5) {
        $codeHTML.='<select name="'.$nomVarSelect.'" class="qcm_select">';
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) $codeHTML.="<option value=\"$i\">$valeur</option>";
		$codeHTML.='</select>';
      } else {
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) 
          $codeHTML.='<input type="radio" class="jeux_radio qcm_radio" name="'.$nomVarSelect
		  	. '" value="'.$i.'" id="'.$nomVarSelect.$i.'"><label for="'.$nomVarSelect.$i.'">&nbsp;'
          	. $valeur.'</label><br />';
       }
       $codeHTML.="</div><br />";
       
    }	// fin du cas sans correction

  // Sinon on affiche la correction
  else {
 	 if ($_POST[$nomVarSelect]) {
		// les points de la reponse donnee...
		$pointsR = $qcms[$indexQCM]['points'][$trou?0:$_POST[$nomVarSelect]];
		
		// la reponse donnee & precision des points eventuels de la mauvaise reponse...
		$intro=$trou?_T('jeux:votre_reponse'):_T('jeux:votre_choix');
		$codeHTML.='<div class="qcm_reponse">'
			 .((($pointsR==$pointsQ) || ($pointsR==0))?$intro:qcm_les_points($intro, $pointsR))
			 .($trou?$_POST[$nomVarSelect]:$qcms[$indexQCM]['propositions'][$_POST[$nomVarSelect]])
			 .'</div>';

		// bonne reponse
		$bonneReponse = ($trou && in_array($_POST[$nomVarSelect], $qcms[$indexQCM]['propositions']))
			|| ($qcms[$indexQCM]['bonnereponse']==$_POST[$nomVarSelect]);

		// si ce n'est pas un trou, on donne les points de la reponse quoiqu'il arrive
		if (!$trou || $bonneReponse) $qcm_score += $pointsR;
			
        if ($bonneReponse)
			$codeHTML .= '<div class="qcm_correction_juste">'._T('qcm:qcm_reponseJuste').'</div>';
         else $codeHTML .= '<div class="qcm_correction_faux">'._T('qcm:qcm_reponseFausse').'</div>';
           
        // les precisions eventuelles
		$prec = $qcms[$indexQCM]['precisions'][$trou?0:$_POST[$nomVarSelect]];
        if ($prec<>"") $codeHTML.="<div align=\"center\"><div class=\"qcm_precision\">$prec</div></div>";

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
		. qcm_affiche_la_question($indexJeux, $indexQCM, isset($_POST["var_correction_".$indexJeux]), $gestionPoints)
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
  $titre = $horizontal = $vertical = $solution = $html = false;

  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('qcm', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_QCM || $valeur==_JEUX_QUIZ) {
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

  // certaines questions ne valent-elles pas 1 point ?
  $gestionPoints = $qcms['totalscore']<>$qcms['nbquestions'];

  // reinserer les qcms mis en forme
  $texte = qcm_inserer_les_qcm($html, $indexJeux, $gestionPoints);

  // calcul des extremes
  $tete = '<div class="jeux qcm">'.($titre?'<div class="jeux_titre qcm_titre">'.$titre.'<hr /></div>':'');
  if (!isset($_POST["var_correction_".$indexJeux])) { 
	$tete .= "\n".'<form method="post" action="">';
	$pied = '<br />
	<input type="hidden" name="var_correction_'.$indexJeux.'" value="yes">
	<div align="center"><input type="submit" value="'._T('jeux:corriger').'" class="jeux_bouton"></div>
	</form>';
  } else {
      $pied = jeux_afficher_score($qcm_score, $qcms['totalscore'])
  			. jeux_bouton_reinitialiser();
  }
  
  unset($qcms); unset($qcm_score);
  return $tete.$texte.$pied.'</div>';
}

?>