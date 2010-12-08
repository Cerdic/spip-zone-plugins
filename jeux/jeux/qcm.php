<?php
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

 Insere un QCM dans vos articles !
----------------------------------
 Idee originale de Mathieu GIANNECCHINI
---------------------------------------

separateurs obligatoires : [qcm], [qrm] ou [quiz]
separateurs optionnels   : [titre], [texte], [config], [score]
parametres de configurations par defaut :
	trou=auto // taille du trou affiche en cas de proposition unique
	une_par_une=non // affiche les questions une par une
	corrections=oui // corrige chaque reponse (juste ou fausse) et affiche les precisions eventuelles
	solution=non // donne la(les) bonne(s) reponse(s) lors de la correction
	points=oui // affiche eventuellement les points dans les questions
	max_radios=5 // nombre maximal de boutons radios affiches avant le choix d'une liste deroulante
	colonnes=1 // nombre de boutons (type radio ou a cocher) par ligne

Exemple de syntaxe dans l'article :
------------------------------------

<jeux>
	[titre]
	Un titre pour le QCM !
	[qcm]
	Q Une question bla bla
	P1 Une proposition 1
	P2 Une proposition 2
	P3 Une proposition 3 (la bonne reponse)
	R3 (la reponse 3 est la bonne !)
	[qcm]
	Q Une question encore
	P1.2 Une proposition a 2 points (la bonne reponse)
	P2 Une proposition 2
	R1
	[qcm]
	Q Une question pour finir
	P1 Une proposition 1|Un commentaire 1
	P2 Une proposition 2|Un commentaire 2
	P3.4 Une proposition a 4 points !|Effectivement !
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

Liste des mots a placer apres "P" : voir le fichier trous.php pour l'utilisation des listes de mots.
La gestion des points et des precisions est toujours possible :
	P.4 mercure venus terre mars jupiter saturne uranus neptune|saviez-vous que pluton n'est plus une planete ?

*/


// cette fonction remplit le tableau $qcms sur la question $indexQCM
function qcm_analyse_le_qcm($qcm, $indexQCM, $isQRM) {
  global $qcms;
  $qcms[$indexQCM]['qrm'] = $isQRM;
  $qcms['qrm'] |= $isQRM;
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
	 	list($reponse, $precision)=explode("|", $li, 2);
		// On extrait le numero de la proposition et son contenu
		preg_match(',^P(\d*)(.*)$,', $reponse, $regs);	
		$indexProposition = intval($regs[1]);
		$suiteProposition = trim($regs[2]);
		$qcms[$indexQCM]['nbpropositions']++;
        // On extrait les points eventuellement associes a chaque reponse
        if(preg_match(',^\.(-?\d+)(.*)$,', $suiteProposition, $regs)){
          $qcms[$indexQCM]['points'][$indexProposition] = intval($regs[1]);
          $qcms[$indexQCM]['propositions'][$indexProposition] = trim($regs[2]);
        } else {
          $qcms[$indexQCM]['points'][$indexProposition] = 0;
          $qcms[$indexQCM]['propositions'][$indexProposition] = $suiteProposition;
        }
		// la precision eventuelle...
       	$qcms[$indexQCM]['precisions'][$indexProposition] = trim($precision);
		// cas d'un trou (ou d'une proposition non numerotee !)
		if ($indexProposition==0) {
			$qcms[$indexQCM]['maxscore'] = $qcms[$indexQCM]['points'][0] = max($qcms[$indexQCM]['points'][0], 1);
			$qcms[$indexQCM]['propositions'] = jeux_liste_mots($qcms[$indexQCM]['propositions'][0]);
			$qcms[$indexQCM]['nbpropositions'] = 1;
		}
		break;
      case 'R' :		// On recupere le numero et les points de la bonne reponse
		// total des points des bonnes reponses
		$qcms[$indexQCM]['maxscore'] = 0;
		// parcours des bonnes reponses
		$t = preg_split(',\s+R,', ' '.$li);
		for ($i=1;$i<count($t);$i++) if (preg_match(',^(\d+),', $t[$i], $regs)) {
			$indexBonneReponse = intval($regs[1]);
			$qcms[$indexQCM]['bonnesreponses'][$indexBonneReponse]=1;
			// au cas ou les points ne sont pas specifies pour la bonne reponse
			if ($qcms[$indexQCM]['points'][$indexBonneReponse]==0) $qcms[$indexQCM]['points'][$indexBonneReponse] = 1;
			// reponse unique : recherche du plus grand score attribue aux bonnes reponses
			// reponses multiples : addition de tous les scores attribues aux bonnes reponses
			$p = $qcms[$indexQCM]['points'][$indexBonneReponse];
			if (!$isQRM) $qcms[$indexQCM]['maxscore'] = max($qcms[$indexQCM]['maxscore'], $p);
			elseif($p>0) $qcms[$indexQCM]['maxscore'] += $p;
		}
		// les reponses fausses deviennent negatives dans le cas de reponses multiples
		if ($isQRM) foreach($qcms[$indexQCM]['points'] as $p=>$v) if ($v==0) $qcms[$indexQCM]['points'][$p] = -1;
		break;

      default : break;
    }
  } // foreach
} // function

function qcm_les_points($phrase, $points, $veto=false) {
	if (!jeux_config('points') || $veto) return $phrase;
    $pointsHTML = '<span class="jeux_points"> ('.$points. _T('jeux:point'.(abs($points)>1?'s':'')).')</span>';
  	if (preg_match(',( ?: *)$,', $phrase, $regs)) 
		$phrase = substr_replace($phrase, $pointsHTML, strlen($phrase)-strlen($regs[1]), 0);
	  else $phrase .= $pointsHTML;
	return $phrase;  
}

function qcm_un_trou($nomVarSelect, $indexQCM) {
  global $qcms;
  if (($sizeInput = intval(jeux_config('trou')))==0)
	foreach($qcms[$indexQCM]['propositions'] as $mot) $sizeInput = max($sizeInput, strlen($mot));
  $temp = $_POST[$nomVarSelect] = trim($_POST[$nomVarSelect]);
  $prop = jeux_minuscules($temp);
  return " &nbsp; &nbsp; &nbsp;<input name=\"$nomVarSelect\" class=\"jeux_input qcm_input\" size=\"$sizeInput\" type=\"text\" /> ";
}

function qcm_affiche_la_question($indexJeux, $indexQCM, $corriger, $gestionPoints) {
  global $qcms, $qcm_score, $qcm_score_detaille;
  $indexQCM_1 = $indexQCM + 1;
  if (!$qcms[$indexQCM]['nbpropositions'] || !$qcms[$indexQCM]['maxscore']) 
  	return "<div class=\"jeux_question\">".definir_puce()._T('jeux:erreur_syntaxe').'</div><br />';

  // Initialisation du code a retourner
  $nomVarSelect = "var{$indexJeux}_Q{$indexQCM}";
  $question = trim(str_replace('&nbsp;', ' ', $qcms[$indexQCM]['question']));
  $trou = $qcms[$indexQCM]['nbpropositions']==1;
  $qrm = $qcms[$indexQCM]['qrm'];
  $nbcol = jeux_config('colonnes');

  // affichage des points dans la question
  if ($gestionPoints) {
    $pointsQ = $qcms[$indexQCM]['maxscore'];
	$question = qcm_les_points($question, $pointsQ);
  } else $pointsQ = 1;

  $codeHTML = "<div class=\"qcm_element \"><div class=\"jeux_question\">".definir_puce().$question.'</div>';
  if (!$corriger){
	// affichage sans correction :
	$codeHTML.="\n<div class=\"qcm_proposition\">";

	if ($trou) {
		$codeHTML.=qcm_un_trou($nomVarSelect, $indexQCM);
	} elseif ($qrm) {
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) 
			$codeHTML.='<input type="checkbox" class="jeux_cocher qcm_cocher" name="'.$nomVarSelect
				. '[]" value="'.$i.'" id="'.$nomVarSelect.$i.'" /><label for="'.$nomVarSelect.$i.'">&nbsp;'
				. $valeur.'</label>'
				. ($i % $nbcol?' &nbsp; ':'<br />');
	// S'il y a trop de choix, on utilise une liste a la place des boutons radio
	} elseif ($qcms[$indexQCM]['nbpropositions']>jeux_config('max_radios')) {
		$codeHTML.='<select name="'.$nomVarSelect.'" class="qcm_select"><option value="">'._T('jeux:votre_choix').'</option>';
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) $codeHTML.="<option value=\"$i\">$valeur</option>";
		$codeHTML.='</select>';
	} else {
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) 
			$codeHTML.='<input type="radio" class="jeux_radio qcm_radio" name="'.$nomVarSelect
				. '" value="'.$i.'" id="'.$nomVarSelect.$i.'" /><label for="'.$nomVarSelect.$i.'">&nbsp;'
				. $valeur.'</label>'
				. ($i % $nbcol?' &nbsp; ':'<br />');
	}
	$codeHTML.="</div><br /></div>";

    }	// fin du cas sans correction

  // Sinon on affiche la correction
  else {
  	 $reponse = $_POST[$nomVarSelect];
	 if (!is_array($reponse)) $reponse=trim($reponse);
	 $bonneReponse = false; $qrm_score = 0;
 	 if ($reponse) {
		// chaque question est-elle corrigee ?
		$affiche_correction = jeux_config('corrections');
		// les points de la reponse donnee...
		$pointsR = 0;
		if (is_array($reponse)) foreach($reponse as $r) $pointsR += $qcms[$indexQCM]['points'][$r]>0?$qcms[$indexQCM]['points'][$r]:0;
		else $pointsR = $qcms[$indexQCM]['points'][$trou?0:$reponse];

		$intro=$trou?_T('jeux:votre_reponse'):_T('jeux:votre_choix');

		// question a reponse simple
		if (!$qrm) {
			// la reponse donnee & precision des points eventuels de la mauvaise reponse...
			$codeHTML.='<div class="qcm_reponse">'
				 .((($pointsR==$pointsQ) || ($pointsR==0))?$intro:qcm_les_points($intro, $pointsR, !$affiche_correction))
				 .($trou?$reponse:$qcms[$indexQCM]['propositions'][$reponse])
				 .'</div>';

			// bonne reponse
			$bonneReponse = ($trou && jeux_in_liste($reponse, $qcms[$indexQCM]['propositions']))
				|| ($qcms[$indexQCM]['bonnesreponses'][$reponse]==1);

			// si ce n'est pas un trou, on donne les points de la reponse quoiqu'il arrive
			if (!$trou || $bonneReponse) $qcm_score += $pointsR;
			// on renseigne le resultat detaille
			$qcm_score_detaille[] = $trou?"T$indexQCM_1:$reponse:".($bonneReponse?$pointsR:'0')
				:"Q$indexQCM_1:R$reponse:$pointsR";

			if($affiche_correction) {
				// reponse juste ou fausse ?
				$codeHTML .= '<div class="qcm_reponse"><span class="qcm_correction_'.($bonneReponse?'juste':'faux').'">'
					._T('jeux:reponse'.($bonneReponse?'Juste':'Fausse')).'</span></div>';
				// les precisions eventuelles
				$prec = $qcms[$indexQCM]['precisions'][$trou?0:$reponse];
				if (strlen($prec)) $codeHTML.="<div class=\"qcm_precision\">$prec</div>";
			}

		// question a reponses multiples
		} else foreach($reponse as $r) {
			// la reponse donnee & precision des points de la mauvaise reponse...
			$codeHTML.='<div class="qcm_reponse">'
				 .qcm_les_points($intro, $qcms[$indexQCM]['points'][$r], !$affiche_correction)
				 .$qcms[$indexQCM]['propositions'][$r]
				 .'</div>';

			// bonne reponse
			$bonneReponse = $qcms[$indexQCM]['bonnesreponses'][$r]==1;
	
			// on donne les points de la reponse quoiqu'il arrive
			$qcm_score += $qcms[$indexQCM]['points'][$r];
			$qcm_score_detaille[] = "Q$indexQCM_1:R$r:".$qcms[$indexQCM]['points'][$r];
			$qrm_score += $qcms[$indexQCM]['points'][$r];
				
			if($affiche_correction) {
				// reponse juste ou fausse ?
				$codeHTML .= '<div class="qcm_reponse"><span class="qcm_correction_'.($bonneReponse?'juste':'faux').'">'
					._T('jeux:reponse'.($bonneReponse?'Juste':'Fausse')).'</span></div>';
				// les precisions eventuelles
				$prec = $qcms[$indexQCM]['precisions'][$r];
				if (strlen($prec)) $codeHTML.="<div class=\"qcm_precision\">$prec</div>";
			}
		}

	// pas de reponse postee...
	} else {
		$codeHTML.='<div class="qcm_correction_null">'._T('jeux:reponseNulle').'</div>';
		$qcm_score_detaille[] = "Q$indexQCM_1:R?:0";
	}

	// on affiche les bonnes reponses si la configuration l'autorise
	if (jeux_config('solution')) {
		if (!$qrm && !$bonneReponse) {
		// on s'occupe d'abord des qcm et des trous
			$codeHTML.='<div class="qcm_reponse">'._T('jeux:bonneReponse').'&nbsp;';
			if ($trou) $codeHTML.="'".join("' "._T('info_ou')."' ", $qcms[$indexQCM]['propositions'])."'";
			else {
				$temp=array();
				foreach($qcms[$indexQCM]['bonnesreponses'] as $i=>$val) if ($qcms[$indexQCM]['bonnesreponses'][$i]==1) {
					$prec = $qcms[$indexQCM]['precisions'][$i];
					$temp[] = $qcms[$indexQCM]['propositions'][$i]
						. (strlen($prec)?"<div class=\"qcm_precision\">$prec</div>":'<br />');
				}
				$codeHTML.=join(''._T('info_ou').' ', $temp);
			}
			$codeHTML.='</div>';
		} elseif($qrm && $qrm_score<>$qcms[$indexQCM]['maxscore']) {
		// on s'occupe ensuite des qrm
			$temp=array();
			foreach($qcms[$indexQCM]['bonnesreponses'] as $i=>$val) {
				if (!is_array($reponse) || !in_array($i, $reponse)) {
					$prec = $qcms[$indexQCM]['precisions'][$i];
					$temp[] = '<div class="qcm_reponse">&nbsp;&#8226;&nbsp;'
						. qcm_les_points($qcms[$indexQCM]['propositions'][$i], $qcms[$indexQCM]['points'][$i]).'</div>'
						. (strlen($prec)?"<div class=\"qcm_precision\">$prec</div>":'<br />');
				}
			}
			if (count($temp)) $codeHTML.='<div class="qcm_reponse"><span class="qcm_correction_juste">'._T('jeux:Correction').'</span></div>'.join('', $temp);
		}
	} // jeux_config('solution')

	$codeHTML.='<br /></div>';
     
  } // fin du cas avec correction
  return $codeHTML;
}

function qcm_inserer_les_qcm(&$chaine, $indexJeux, $gestionPoints) {
  global $qcms;
  if (preg_match(',<ATTENTE_QCM>(\d+)</ATTENTE_QCM>,', $chaine, $regs)) {
	$indexQCM = intval($regs[1]);
	list($texteAvant, $texteApres) = explode($regs[0], $chaine, 2); 
	$chaine = $texteAvant.jeux_rem('QCM-DEBUT', $indexQCM)
		. qcm_affiche_la_question($indexJeux, $indexQCM, isset($_POST["var_correction_".$indexJeux]), $gestionPoints)
		. jeux_rem('QCM-FIN', $indexQCM)
		. qcm_inserer_les_qcm($texteApres, $indexJeux, $gestionPoints); 
  }
  return $chaine;
}

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_qcm_init() {
	return "
		trou=auto	// taille du trou affiche en cas de proposition unique
		une_par_une=non // affiche les questions une par une
		corrections=oui // corrige chaque reponse (juste ou fausse) et affiche les precisions eventuelles
		solution=non	// donne la(les) bonne(s) reponse(s) lors de la correction
		points=oui // affiche eventuellement les points dans les questions
		max_radios=5 // nombre maximal de boutons radios affiches avant le choix d'une liste deroulante
		colonnes=1 // nombre de boutons par ligne
	";
}

// traitement du jeu : jeu_{mon_jeu}()
function jeux_qcm($texte, $indexJeux, $form=true) {
  // initialisation  
  global $qcms, $qcm_score, $qcm_score_detaille;

  $qcms = array(); $indexQCM = $qcm_score = 0;
  $qcm_score_detaille = array();
  $qcms['nbquestions'] = $qcms['totalscore'] = $qcms['totalpropositions'] = 0;
  $titre = $horizontal = $vertical = $solution = $html = $categ_score = false;

  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('qcm', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_QCM || $valeur==_JEUX_QUIZ || $valeur==_JEUX_QRM) {
		// remplacement des qcm par : <ATTENTE_QCM>ii</ATTENTE_QCM>
		$html .= "<ATTENTE_QCM>$indexQCM</ATTENTE_QCM>";
		// On analyse le QCM
		qcm_analyse_le_qcm($tableau[$i+1], $indexQCM, $valeur==_JEUX_QRM);
	    $qcms['totalpropositions'] +=  count($qcms[$indexQCM]['propositions']);
    	$qcms['totalscore'] +=  $qcms[$indexQCM]['maxscore'];
	  	$indexQCM++;
	  }
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_SCORE) $categ_score = $tableau[$i+1];
  }

  // si un qrm a ete insere ou si certaines questions ne valent pas 1 point, on affiche les points
  $gestionPoints = $qcms['qrm'] || ($qcms['totalscore']<>$qcms['nbquestions']);

  // reinserer les qcms mis en forme
  $texte = qcm_inserer_les_qcm($html, $indexJeux, $gestionPoints);

  // calcul des extremes
  $tete = '<div class="jeux_cadre qcm">'.($titre?'<div class="jeux_titre qcm_titre">'.$titre.'<hr /></div>':'');
  $pied = '';
  if (!isset($_POST["var_correction_".$indexJeux])) {
	if($form) {
		$pied = '<br />'.jeux_bouton_corriger().jeux_form_fin();
		$tete .= jeux_form_debut('qcm', $indexJeux);
	}
  } else {
	$pied = jeux_afficher_score($qcm_score, $qcms['totalscore'], _request('id_jeu'), join(', ', $qcm_score_detaille), $categ_score);
	if($form) $pied .= jeux_bouton_reinitialiser();
  }
  // ajout du javascript si on doit afficher une par une
  if (jeux_config('une_par_une'))
  	$js = '<script type="text/javascript">qcm_affichage_une_par_une();</script>';
  else
  	$js = '';

  unset($qcms, $qcm_score, $qcm_score_detaille);
  return $tete.$texte.$pied.'</div>'.$js;
}

?>
