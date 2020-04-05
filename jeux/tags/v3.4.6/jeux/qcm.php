<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

 Insere un QCM dans vos articles !
----------------------------------
 Idee originale de Mathieu GIANNECCHINI
---------------------------------------

separateurs obligatoires : [qcm], [qrm] ou [quiz]
separateurs optionnels   : [titre], [texte], [config], [score]
parametres de configuration par defaut :
	voir la fonction jeux_qcm_init() ci-dessous

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
		bouton_corriger=corriger // fond utilise pour le bouton 'Corriger'
		bouton_refaire=recommencer // fond utilise pour le bouton 'Reset'
	";
}

// cette fonction remplit le tableau $qcms sur la question $indexQCM
function qcm_analyse_le_qcm(&$qcms, $qcm, $indexQCM, $isQRM) {
  // init
  isset($qcms['qrm']) || $qcms['qrm'] = false;
  isset($qcms[$indexQCM]['nbpropositions']) || $qcms[$indexQCM]['nbpropositions'] = 0;
  
  $qcms[$indexQCM]['qrm'] = $isQRM;
  $qcms['qrm'] |= $isQRM;
  $lignes = preg_split('/[\r\n]+/', $qcm);
  foreach($lignes as $ligne) {
	$li = trim($ligne);
	if (!$li) continue;

    switch($li[0]){
      case 'Q' : 	  	// extraire la question
		$qcms[$indexQCM]['question'] = trim(substr($li, 1));
		$qcms[$indexQCM]['maxScore'] = 0;
		$qcms['nbQuestions']++;
        break;
      case 'P' : 	  	// extraire une proposition
	  	// Pour les precisions de la proposition...
	 	list($reponse, $precision) = array_pad(explode("|", $li, 2), 2, "");
		// extraire le numero de la proposition et son contenu
		preg_match(',^P(\d*)(.*)$,', $reponse, $regs);	
		$indexProposition = intval($regs[1]);
		$suiteProposition = trim($regs[2]);
		$qcms[$indexQCM]['nbpropositions']++;
        // extraire les points eventuellement associes a chaque reponse
        if(preg_match(',^\.(-?\d+)(.*)$,', $suiteProposition, $regs)){
          $qcms[$indexQCM]['points'][$indexProposition] = intval($regs[1]);
          $qcms[$indexQCM]['propositions'][$indexProposition] = trim($regs[2]);
          $qcms['pointsTrouves'] = 1;
        } else {
          $qcms[$indexQCM]['points'][$indexProposition] = false;
          $qcms[$indexQCM]['propositions'][$indexProposition] = $suiteProposition;
        }
		// la precision eventuelle...
       	$qcms[$indexQCM]['precisions'][$indexProposition] = trim($precision);
		// cas d'un trou (ou d'une proposition non numerotee !)
		if ($indexProposition==0) {
			$qcms[$indexQCM]['maxScore'] = $qcms[$indexQCM]['points'][0] = 
				$qcms[$indexQCM]['points'][0]===false?1:$qcms[$indexQCM]['points'][0];
			$qcms[$indexQCM]['propositions'] = jeux_liste_mots($qcms[$indexQCM]['propositions'][0]);
			$qcms[$indexQCM]['nbpropositions'] = 1;
		}
		break;
      case 'R' :		// recuperer le numero et les points de la bonne reponse
		// total des points des bonnes reponses
		$qcms[$indexQCM]['maxScore'] = 0;
		// parcours des bonnes reponses
		$t = preg_split(',\s+R,', ' '.$li);
		for ($i=1;$i<count($t);$i++) if (preg_match(',^(\d+),', $t[$i], $regs)) {
			$indexBonneReponse = intval($regs[1]);
			$qcms[$indexQCM]['bonnesreponses'][$indexBonneReponse]=1;
			// au cas ou les points ne sont pas specifies pour la bonne reponse
			if ($qcms[$indexQCM]['points'][$indexBonneReponse]===false) $qcms[$indexQCM]['points'][$indexBonneReponse] = 1;
			// reponse unique : recherche du plus grand score attribue aux bonnes reponses
			// reponses multiples : addition de tous les scores attribues aux bonnes reponses
			$p = $qcms[$indexQCM]['points'][$indexBonneReponse];
			if (!$isQRM) $qcms[$indexQCM]['maxScore'] = max($qcms[$indexQCM]['maxScore'], $p);
			elseif($p>0) $qcms[$indexQCM]['maxScore'] += $p;
		}
		// les reponses fausses deviennent negatives dans le cas de reponses multiples
		if ($isQRM) foreach($qcms[$indexQCM]['points'] as $p=>$v) if ($v===false) $qcms[$indexQCM]['points'][$p] = -1;
		break;

      default : break;
    }
  } // foreach
} // function

function qcm_les_points($phrase, $points, $veto=false) {
	if (!jeux_config('points') || $veto || $points===false) return $phrase;
    $pointsHTML = '<span class="jeux_points"> ('.$points. _T('jeux:point'.(abs($points)>1?'s':'')).')</span>';
 	if (preg_match(',((?:\xc2\xa0| )?: *)$,', $phrase, $regs)) 
		$phrase = substr_replace($phrase, $pointsHTML, strlen($phrase)-strlen($regs[1]), 0);
	  else $phrase .= $pointsHTML;
	return $phrase;  
}

function qcm_affiche_la_question(&$qcms, $indexJeux, $indexQCM, $gestionPoints) {
  $indexQCM_1 = $indexQCM + 1;
  if (!$qcms[$indexQCM]['nbpropositions'] || !$qcms[$indexQCM]['maxScore']) 
  	return '<div class="jeux_question">'.definir_puce()._T('jeux:erreur_syntaxe').'</div>';

  // Initialisation du code a retourner
  list($idInput, $nameInput) = jeux_idname($indexJeux, $indexQCM, 'Q');
  $question = trim(str_replace('&nbsp;', ' ', $qcms[$indexQCM]['question']));
  $trou = $qcms[$indexQCM]['nbpropositions']==1;
  $qrm = $qcms[$indexQCM]['qrm'];
  $nbcol = jeux_config('colonnes');

  // affichage des points dans la question
  if ($gestionPoints) {
    $pointsQ = $qcms[$indexQCM]['maxScore'];
	$question = qcm_les_points($question, $pointsQ);
  } else $pointsQ = 1;

  $codeHTML = "<div class='qcm_element'><div class='jeux_question'>".definir_puce().$question.'</div>';
  if (!jeux_form_correction($indexJeux)){
	// affichage du jeu sans correction
	$codeHTML .= "\n<div class='qcm_proposition'>";

	if ($trou) {
		if (($sizeInput = intval(jeux_config('trou')))==0)
			foreach($qcms[$indexQCM]['propositions'] as $mot) $sizeInput = max($sizeInput, strlen($mot));
		$prop = jeux_minuscules($temp);
		$codeHTML .= " &nbsp; &nbsp; &nbsp;<input name='$nameInput' id='$idInput' class='jeux_input qcm_input' size='$sizeInput' type='text' /> ";
	} elseif ($qrm) {
		// cases a cocher
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) 
			$codeHTML .= "<input type='checkbox' class='jeux_cocher qcm_cocher' name='{$nameInput}[]' value='$i' id='{$idInput}-$i' /><label for='{$idInput}-$i'>&nbsp;"	. $valeur.'</label>' . ($i % $nbcol?' &nbsp; ':'<br />');
	// S'il y a trop de choix, utiliser une liste a la place des boutons radio
	} elseif ($qcms[$indexQCM]['nbpropositions']>jeux_config('max_radios')) {
		$codeHTML .= "<select name='$nameInput' id='$idInput' class='qcm_select'><option value=''>"._T('jeux:votre_choix').'</option>';
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) $codeHTML.="<option value='$i'>$valeur</option>";
		$codeHTML .= '</select>';
	} else {
		// boutons radio
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) 
			$codeHTML .= "<input type='radio' class='jeux_radio qcm_radio' name='$nameInput' value='$i' id='{$idInput}-$i' /><label for='{$idInput}-$i'>&nbsp;$valeur</label>" . ($i % $nbcol?' &nbsp; ':'<br />');
	}
	$codeHTML .= '</div><br /></div>';

  }	else {
	 // affichage du jeu avec correction
	 $reponse = jeux_form_reponse($indexJeux, $indexQCM, 'Q');
	 $bonneReponse = false; $qrm_score = 0;
 	 if($reponse) {
		// chaque question est-elle corrigee ?
		$affiche_correction = jeux_config('corrections');
		// les points de la reponse donnee...
		$pointsR = false;
		if (is_array($reponse)) {
			foreach($reponse as $r) 
				if(($p=$qcms[$indexQCM]['points'][$r])!==false) $pointsR += $p;
		} else {
			if(($p=$qcms[$indexQCM]['points'][$trou?0:$reponse])!==false) $pointsR += $p;
		}

		$intro = $trou?_T('jeux:votre_reponse'):_T('jeux:votre_choix');

		if (!$qrm) {
			// ici : une question a reponse simple
			// la reponse donnee & precision des points eventuels de la mauvaise reponse...
			$codeHTML .= '<div class="qcm_reponse">'
				 . ($pointsR===$pointsQ?$intro:qcm_les_points($intro, $pointsR, !$affiche_correction))
				 . ($trou?$reponse:$qcms[$indexQCM]['propositions'][$reponse])
				 . '</div>';

			// bonne reponse
			$bonneReponse = ($trou && jeux_in_liste($reponse, $qcms[$indexQCM]['propositions']))
				|| (isset($qcms[$indexQCM]['bonnesreponses'][$reponse])
					and $qcms[$indexQCM]['bonnesreponses'][$reponse]==1);

			// si ce n'est pas un trou, donner les points de la reponse quoiqu'il arrive
			if (!$trou || $bonneReponse) $qcms['score_du_qcm'] += $pointsR;
			// renseigner le resultat detaille
			$qcms['score_detaille'][] = $trou?"T$indexQCM_1:$reponse:".($bonneReponse?$pointsR:'0')
				:"Q$indexQCM_1:R$reponse:$pointsR";

			if($affiche_correction) {
				// reponse juste ou fausse ?
				$codeHTML .= '<div class="qcm_correction"><span class="qcm_correction_'.($bonneReponse?'juste':'faux').'">'
					._T('jeux:reponse'.($bonneReponse?'Juste':'Fausse')).'</span></div>';
				// les precisions eventuelles
				$prec = $qcms[$indexQCM]['precisions'][$trou?0:$reponse];
				if (strlen($prec)) $codeHTML.="<div class=\"qcm_precision\">$prec</div>";
			}
		} else foreach($reponse as $r) {
			// ici : une question a reponses multiples
			// la reponse donnee & precision des points de la mauvaise reponse...
			$codeHTML.='<div class="qcm_reponse">'
				 .qcm_les_points($intro, $qcms[$indexQCM]['points'][$r], !$affiche_correction)
				 .$qcms[$indexQCM]['propositions'][$r]
				 .'</div>';

			// bonne reponse
			$bonneReponse = $qcms[$indexQCM]['bonnesreponses'][$r]==1;
	
			// donner les points de la reponse quoiqu'il arrive
			$qcms['score_du_qcm'] += $qcms[$indexQCM]['points'][$r];
			$qcms['score_detaille'][] = "Q$indexQCM_1:R$r:".intval($qcms[$indexQCM]['points'][$r]);
			$qrm_score += $qcms[$indexQCM]['points'][$r];
				
			if($affiche_correction) {
				// reponse juste ou fausse ?
				$codeHTML .= '<div class="qcm_reponse"><span class="qcm_correction_'.($bonneReponse?'juste':'faux').'">'
					._T('jeux:reponse'.($bonneReponse?'Juste':'Fausse')).'</span></div>';
				// les precisions eventuelles
				$prec = $qcms[$indexQCM]['precisions'][$r];
				if (strlen($prec)) $codeHTML.="<div class=\"qcm_precision\">$prec</div>";
			}
		} // foreach($reponse)

	// pas de reponse postee...
	} else {
		$codeHTML.='<div class="qcm_correction_null">'._T('jeux:reponseNulle').'</div>';
		$qcms['score_detaille'][] = "Q$indexQCM_1:R?:0";
	}

	// afficher les bonnes reponses si la configuration l'autorise
	if (jeux_config('solution')) {
		if (!$qrm && !$bonneReponse) {
		// s'occuper d'abord des qcm et des trous
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
		} elseif($qrm && $qrm_score<>$qcms[$indexQCM]['maxScore']) {
		// s'occuper ensuite des qrm
			$temp=array();
			foreach($qcms[$indexQCM]['bonnesreponses'] as $i=>$val) {
				if (!is_array($reponse) || !in_array($i, $reponse)) {
					$prec = $qcms[$indexQCM]['precisions'][$i];
					$temp[] = '<div class="qcm_reponse">&nbsp;&#8226;&nbsp;'
						. qcm_les_points($qcms[$indexQCM]['propositions'][$i], $qcms[$indexQCM]['points'][$i]).'</div>'
						. (strlen($prec)?"<div class=\"qcm_precision\">$prec</div>":'<br />');
				}
			}
			if (count($temp)) $codeHTML.='<div class="qcm_reponse"><span class="qcm_correction_juste">'._T('jeux:correction').'</span></div>'.join('', $temp);
		}
	} // jeux_config('solution')

	$codeHTML.='<br /></div>';
     
  } // fin du cas avec correction
  return $codeHTML;
}

function qcm_inserer_les_qcm(&$qcms, $indexJeux, &$chaine, $gestionPoints) {
  if (preg_match(',<ATTENTE_QCM>(\d+)</ATTENTE_QCM>,', $chaine, $regs)) {
	$indexQCM = intval($regs[1]);
	list($texteAvant, $texteApres) = explode($regs[0], $chaine, 2); 
	$chaine = $texteAvant.jeux_rem('QCM-DEBUT', $indexQCM)
		. qcm_affiche_la_question($qcms, $indexJeux, $indexQCM, $gestionPoints)
		. jeux_rem('QCM-FIN', $indexQCM)
		. qcm_inserer_les_qcm($qcms, $indexJeux, $texteApres, $gestionPoints); 
  }
  return $chaine;
}

// traitement du jeu : jeu_{mon_jeu}()
function jeux_qcm($texte, $indexJeux, $form=true) {
  // initialisation  
  $qcms_[$indexJeux] = array(
  	'pointsTrouves' =>0, 'nbQuestions' =>0, 
	'totalScore' => 0, 'totalPropositions' => 0,
  	'score_du_qcm' => 0, 'score_detaille' => array()
  ); 
  $qcms = &$qcms_[$indexJeux];
  $indexQCM = 0;
  $titre = $horizontal = $vertical = $solution = $html = $categ_score = false;
  $id_jeu = _request('id_jeu');

  // parcourir tous les [separateurs]
  $tableau = jeux_split_texte('qcm', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_QCM || $valeur==_JEUX_QUIZ || $valeur==_JEUX_QRM) {
		// remplacement des qcm par : <ATTENTE_QCM>ii</ATTENTE_QCM>
		$html .= "<ATTENTE_QCM>$indexQCM</ATTENTE_QCM>";
		// analyser le QCM
		qcm_analyse_le_qcm($qcms, $tableau[$i+1], $indexQCM, $valeur==_JEUX_QRM);
	    $qcms['totalPropositions'] +=  count($qcms[$indexQCM]['propositions']);
    	$qcms['totalScore'] +=  $qcms[$indexQCM]['maxScore'];
	  	$indexQCM++;
	  }
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_SCORE) $categ_score = $tableau[$i+1];
  }

  // si un qrm a ete insere ou si certaines questions ne valent pas 1 point, afficher les points
  $gestionPoints = $qcms['qrm'] || $qcms['pointsTrouves'];

  // reinserer les qcms mis en forme
  $texte = qcm_inserer_les_qcm($qcms, $indexJeux, $html, $gestionPoints);

  // calcul des extremes
  $tete = '<div class="jeux_cadre qcm">'.($titre?'<div class="jeux_titre qcm_titre">'.$titre.'<hr /></div>':'');
  $pied = '';
  if (jeux_form_correction($indexJeux)) {
	// mode correction 
	$pied = jeux_afficher_score($qcms['score_du_qcm'], $qcms['totalScore'], $id_jeu, join(', ', $qcms['score_detaille']), $categ_score);
	if($form) $pied .= jeux_bouton(jeux_config('bouton_refaire'), $id_jeu, $indexJeux);
  } else {
	// mode formulaire
	if($form) {
		$pied = '<br />' . jeux_bouton(jeux_config('bouton_corriger'), $id_jeu) . jeux_form_fin();
		$tete .= jeux_form_debut('qcm', $indexJeux, '', 'post', self());
	}
  }
  // ajout du javascript s'il faut afficher une par une
  $js = jeux_config('une_par_une')?'<script type="text/javascript">qcm_affichage_une_par_une();</script>':'';

  return $tete.$texte.$pied.'</div>'.$js;
}

?>
