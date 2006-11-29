<?php
/*
insere un QCM dans vos articles !
---------------------------------

balises : <jeux></jeux>
separateurs obligatoires : #TITRE, #QCM
separateurs optionnels   : #HTML

Ensemble de syntaxe dans l'article :
------------------------------------

<jeux>
	#TITRE
	Un titre pour le QCM !

	#QCM
	Q Une question bla bla
	P1 Une proposition 1
	P2 Une proposition 2
	P3 Une proposition 3 (la bonne réponse)
	R3 (la réponse 3 est la bonne !)

	#QCM
	Q Une question encore
	P1.2 Une proposition à 2 points (la bonne réponse)
	P2 Une proposition 2
	R1

	#QCM
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
  						patfr@ifrance.com
 						(sur une idee originale de Mathieu GIANNECCHINI, 2003) 
  Date               : 	30 octobre 2006
  Fonction du plugin : 	Parse le code du QCM tape dans SPIP et stocke
                       	les questions, reponses et commentaires
                       	dans un tableau et retourne le code HTML du QCM
  Mode d'emploi		 :	http://www.spip-contrib.net/Un-QCM-dans-vos-articles
  
  Titre du QCM : 
	- soit sur une ligne de la forme 'T Voici mon titre' placee entre les
	  balises <qcm> et </qcm>
  	- soit entre les balises <qcm-titre> et </qcm-titre>
	- soit entre les balises <intro> et </intro>
		(Spip s'en servira egalement en cas d'absence de descriptif pour 
		calculer la balise #INTRODUCTION utilisee pour resumer l'article)

  Calcul de #INTRODUCTION : si introduction() n'est pas surchargee, Spip cherche 
  d'abord le descriptif, puis en cas d'echec, le contenu du texte situé entre 
  les balises <intro> et </intro>. En dernier lieu, Spip utilise les 500 premiers 
  caractères du chapeau suivi du texte.
  Attention donc : pour ne pas faire apparaitre le contenu du QCM avec les 
  reponses, il vaut mieux penser à bien définir :
  	- soit le descriptif de l'article 
	- soit une introduction placee entre les balises <intro> et </intro>
		(utiliser dans ce cas les balises <qcm-titre> et </qcm-titre>
		pour definir le titre du QCM)
	- soit le titre du QCM place entre les balises <intro> et </intro>
  
*/
 
 define(_QCM_TITRE_DEBUT, '<qcm-titre>');
 define(_QCM_TITRE_FIN, '</qcm-titre>');
 
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
  $chaine = $texteAvant.'<!QCM-DEBUT-#0>'.$texteApres;
  return trim($texte);
}


// cette fonction modifie $chaine et retourne true si un qcm est trouve, false dans le cas contraire
function qcm_recupere_une_question(&$chaine, &$indexQCM, &$titreQCM) {
  global $qcms;
  
  // si les balises ouvrantes et fermantes ne sont pas presentes
  // if (strpos($chaine, _JEUX_DEBUT)===false || strpos($chaine, _JEUX_FIN)===false) return false;

  // remplacement des qcm par : <ATTENTE_QCM>ii</ATTENTE_QCM>
  // list($texteAvant, $suite) = explode(_JEUX_DEBUT, $chaine, 2); 
  // list($qcm, $texteApres) = explode(_JEUX_FIN, $suite, 2); 
  $chaine = "$texteAvant<ATTENTE_QCM>$indexQCM</ATTENTE_QCM>$texteApres";
 
  // On analyse le QCM
  qcm_analyse_le_qcm($qcm, $indexQCM, $titreQCM);
  
  return true;
}

function qcm_les_points($phrase, $points) {
    $pointsHTML = '<span class="spip_qcm_point"> ('.$points. _T('qcm:qcm_point'.($points>1?'s':'')).')</span>';
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

  $codeHTML = "<div class=\"spip_qcm_question\">".definir_puce().$question.'</div>';
  if (!$corrigee){
    // affichage sans correction :
     $codeHTML.="\n<div class=\"spip_qcm_proposition\">";

      // Si il ya plus de 5 choix, on utilise une liste
      if(count($qcms[$indexQCM]['propositions'])>5){
        $codeHTML.='<select name="'.$nomVarSelect.'" class="spip_qcm_select">';
		foreach($qcms[$indexQCM]['propositions'] as $i=>$v) $codeHTML.="<option value=\"$i\">$v</option>";
		$codeHTML.='</select>';
      }
      // Sinon des radio boutons
      else {
		foreach($qcms[$indexQCM]['propositions'] as $i=>$v) 
          $codeHTML.='<input type="radio" name="'.$nomVarSelect
		  	. '" value="'.$i.'" id="'.$nomVarSelect.$i.'"><label for="'.$nomVarSelect.$i.'">'
          	. $v.'</label><br />';
       }
       $codeHTML.="</div><br />";
       
    }	//Fin du cas sans correction

  // Sinon on affiche la correction
  else {
 
	 if ($_POST[$nomVarSelect]) {
		// les points de la reponse donnee...
		$pointsR = $qcms[$indexQCM]['points'][$_POST[$nomVarSelect]];
		
		// la reponse donnee & precision des points eventuels de la mauvaise reponse...
		$codeHTML.='<div class="spip_qcm_reponse">'
			 .((($pointsR==$pointsQ) || ($pointsR==0))?_T('qcm:qcm_introReponse'):qcm_les_points(_T('qcm:qcm_introReponse'), $pointsR))
			 .$qcms[$indexQCM]['propositions'][$_POST[$nomVarSelect]].'</div>';

		// on donne les points de la reponse quoiqu'il arrive
		$qcm_score += $pointsR;
		
        if ($qcms[$indexQCM]['bonnereponse']==$_POST[$nomVarSelect]) 
        // Si c'est juste
			$codeHTML .= '<div class="spip_qcm_correction_juste">'._T('qcm:qcm_reponseJuste').'</div>';
        // Si c'est faux
         else $codeHTML .= '<div class="spip_qcm_correction_faux">'._T('qcm:qcm_reponseFausse').'</div>';
           
        // les precisions eventuelles
        if ($qcms[$indexQCM]['precisions'][$_POST[$nomVarSelect]]<>"")
             $codeHTML.='<div align="center"><div class="spip_qcm_precision">'
				 .$qcms[$indexQCM]['precisions'][$_POST[$nomVarSelect]]
	     	 	 .'</div></div>';

	// pas de reponse postee...
	} else $codeHTML.='<div class="spip_qcm_correction_null">'._T('qcm:qcm_reponseNulle').'</div>';
	   
	$codeHTML.='<br />';
     
  } //Fin du cas avec correction
  return $codeHTML;
}

function qcm_inserer_les_qcm(&$chaine, $gestionPoints) {
  global $qcms;
  if (ereg('<ATTENTE_QCM>([0-9]+)</ATTENTE_QCM>', $chaine, $eregResult)) {
	$indexQCM = intval($eregResult[1]);
	list($texteAvant, $texteApres) = explode($eregResult[0], $chaine, 2); 
	$chaine = "$texteAvant<!QCM-DEBUT-#$indexQCM>\n"
		. qcm_affiche_la_question($indexQCM, isset($_POST["var_correction"]), $gestionPoints)
		. "<!QCM-FIN-#$indexQCM>\n"
		. qcm_inserer_les_qcm($texteApres, $gestionPoints); 
  }
  return $chaine;
}

function jeux_qcm($chaine) {
  define(_JEUX_REM_DEBUT, code_echappement('<!-- '));
  define(_JEUX_REM_FIN, code_echappement(' -->'));

  // initialisation  
  global $qcms, $qcm_score;
  $titreQCM = false; 
  $indexQCM =  $qcm_score = 0;
  $qcms['nbquestions'] = $qcms['totalscore'] = $qcms['totalpropositions'] = 0;
  $tableau = preg_split('/('._JEUX_TITRE.'|'._JEUX_QCM.'|'._JEUX_HTML.')/', 
			_JEUX_HTML.trim($chaine), -1, PREG_SPLIT_DELIM_CAPTURE);
  $titre = $horizontal = $vertical = $solution = $html = false;

  // parcourir toutes les #BALISES
  foreach($tableau as $i => $v){
  	 $v = trim($v);
	 if ($v==_JEUX_TITRE) $titre = trim($tableau[$i+1]);
	  elseif ($v==_JEUX_QCM) {
		// remplacement des qcm par : <ATTENTE_QCM>ii</ATTENTE_QCM>
		$html .= "<ATTENTE_QCM>$indexQCM</ATTENTE_QCM>";
		// On analyse le QCM
		qcm_analyse_le_qcm($tableau[$i+1], $indexQCM);
	    $qcms['totalpropositions'] +=  count($qcms[$indexQCM]['propositions']);
    	$qcms['totalscore'] +=  $qcms[$indexQCM]['maxscore'];
	  	$indexQCM++;
	  }
	  elseif ($v==_JEUX_HTML) $html .= trim($tableau[$i+1]);
  }

  // est-ce certaines questions ne valent pas 1 point ?
  $gestionPoints = $qcms['totalscore']<>$qcms['nbquestions'];

  // trouver un titre, coute que coute...
//  if (!$titre) $titre = qcm_recupere_le_titre($chaine, _QCM_TITRE_DEBUT, _QCM_TITRE_FIN);
//  if (!$titre) $titre = qcm_recupere_le_titre($chaine, '<intro>', '</intro>');
  if (!$titre) $titre = _T('qcm:qcm_titre');
  
  // reinserer les qcms mis en forme
  $chaine = qcm_inserer_les_qcm($html, $gestionPoints);

  $tete = '<div class="spip_qcm"><div class="spip_qcm_titre">'.$titre.'<hr /></div>';
  if (!isset($_POST["var_correction"])) { 
	$tete .= '<form method="post" action="">';
	$pied = '<br>
	<input type="hidden" name="var_correction" value="yes">
	<div align="center"><input type="submit" value="'._T('qcm:qcm_corriger').'" class="spip_qcm_bouton_corriger"></div>
	</form>';
  } else {
      // On ajoute le score final
      $pied = '<center><div class="spip_qcm_score">'._T('qcm:qcm_score')
	  			. "&nbsp;$qcm_score&nbsp;/&nbsp;".$qcms['totalscore'].'<br>'
				. ($qcm_score==$qcms['totalscore']?_T('qcm:qcm_bravo'):'').'</div></center>'
				. '<div class="spip_qcm_bouton_corriger" align="right">[ <a href="'
				. parametre_url(self(),'var_mode','recalcul').'">'._T('qcm:qcm_reinitialiser').'</a> ]</div>';
  }
  
  unset($qcms); unset($qcm_score);
  return $tete.$html.$pied;
}

function jeux_qcm2($chaine){
 if (ereg(_QCM_DEBUT, $chaine)) {
	ob_start();
	$chaine = qcm($chaine);
	$data = ob_get_contents();
	ob_end_clean();
	$chaine = nl2br(str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$data)).$chaine;
 }
 return $chaine;
}


?>