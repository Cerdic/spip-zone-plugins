<?php
/*
  Nom du plugin      : 	Un QCM dans vos articles
  Auteur             : 	Patrice VANNEUFVILLE
 						(sur une idee originale de Mathieu GIANNECCHINI, 2003) 
  Date               : 	30 octobre 2006
  Fonction du plugin : 	Parse le code du QCM tape dans SPIP et stocke
                       	les questions, reponses et commentaires
                       	dans un tableau et retourne le code HTML du QCM
  Syntaxe 			 : 	Dans le texte de l’article, le code du qcm doit 
  					   	se trouver entre les balises minuscules :
							"<qcm>" et "</qcm>".
  Entre ces balises  :					 
						T Titre du qcm
						Q Intitule de la question
						P1 Texte de la proposition 1
						P2 Texte de la proposition 2
						...
						Pn Texte de la proposition n
						R suivi du numero de la proposition indique la bonne reponse. 
						(Donc si la bonne réponse est la proposition 1 on indiquera "R1")
						
						On peut mettre plusieurs couples "<qcm>" et "</qcm>" 
						dans le meme article et inserer des elements html 
						entre ces couples.
						Dans ce cas, on peut utiliser une balise particuliere 
						pour le titre :
							<qcm>
							T Titre du qcm
							</qcm>

						Exemple de gestion des points attribues (positifs ou negatifs) :
							P1.2 Texte de la proposition 1
							P2.-1 Texte de la proposition 2
							R1
						Veuillez a ce que la bonne reponse attribue toujours
						le nombre maximal de points
						
						Lors de la correction il est possible d'inserer un commentaire
						en cas de reponse juste ou de reponse fausse :
							Q Intitule de la question
							P1 Proposition 1|Commentaire de la reponse fausse
							P2 Proposition 2|Commentaire de la reponse juste
							R2
 */
 
 define(_QCM_DEBUT, '<qcm>');
 define(_QCM_FIN, '</qcm>');
 
// cette fonction remplit le tableau $qcms sur la question $indexQCM
function qcm_analyse_le_qcm($qcm, $indexQCM, &$titreQCM) {
  global $qcms;
  $lignes = split("\n", $qcm);
  foreach ($lignes as $ligne) {
	$li=trim($ligne); 
    switch($li[0]){
      case 'T' : 
	  	// On extrait le titre
	  	$titreQCM=substr($li,1);
		break;

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

// cette fonction retourne true si un qcm est trouve, false dans le cas contraire
function qcm_recupere_une_question(&$chaine, &$indexQCM, &$titreQCM) {
  global $qcms;
  
  // si les balises ouvrantes et fermantes ne sont pas presentes
  if (strpos($chaine, _QCM_DEBUT)===false || strpos($chaine, _QCM_FIN)===false) return false;

  // remplacement des qcm par : <ATTENTE_QCM>ii</ATTENTE_QCM>
  list($texteAvant, $suite) = explode(_QCM_DEBUT, $chaine, 2); 
  list($qcm, $texteApres) = explode(_QCM_FIN, $suite, 2); 
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
	$chaine = "$texteAvant<!-- QCM-DEBUT #$indexQCM -->\n"
		. qcm_affiche_la_question($indexQCM, isset($_POST["var_correction"]), $gestionPoints)
		. "<!-- QCM-FIN #$indexQCM -->\n"
		. qcm_inserer_les_qcm($texteApres, $gestionPoints); 
  }
  return $chaine;
}

function qcm_qcm($chaine) {
  // premiere verification
  if (strpos($chaine, _QCM_DEBUT)===false || strpos($chaine, _QCM_FIN)===false) return $chaine;

  // initialisation  
  global $qcms, $qcm_score;
  $titreQCM = _T('qcm:qcm_titre');
  $indexQCM =  $qcm_score = 0;
  $qcms['nbquestions'] = $qcms['totalscore'] = $qcms['totalpropositions'] = 0;
  
  while (qcm_recupere_une_question($chaine, $indexQCM, $titreQCM)) {
    $qcms['totalpropositions'] +=  count($qcms[$indexQCM]['propositions']);
    $qcms['totalscore'] +=  $qcms[$indexQCM]['maxscore'];
  	$indexQCM++;
  }
  
  // est-ce certaines questions ne valent pas 1 point ?
  $gestionPoints = $qcms['totalscore']<>$qcms['nbquestions'];

  // reinserer les qcms mis en forme
  $chaine = qcm_inserer_les_qcm($chaine, $gestionPoints);

  $tete = '<div class="spip_qcm"><div class="spip_qcm_titre">'.$titreQCM.'<hr /></div>';
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
     // unset($_POST["var_correction"]);
  }
  $chaine = str_replace('<!-- QCM-DEBUT #0 -->', $tete.'<!-- QCM-DEBUT #0 -->', $chaine);
  $chaine = str_replace('<!-- QCM-FIN #'.($indexQCM-1).' -->', $pied.'</div>', $chaine);

  unset($qcms);
  return $chaine;
}

function qcm_qcm2($chaine){
 if (ereg(_QCM_DEBUT, $chaine)) {
	ob_start();
	$chaine = qcm($chaine);
	$data = ob_get_contents();
	ob_end_clean();
	$chaine = nl2br(str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$data)).$chaine;
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
	return qcm_qcm($texte);
}	

?>