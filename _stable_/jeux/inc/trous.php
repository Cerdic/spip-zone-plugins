<?php

# il s'agit ici simplement de textes à trous.
# lien à voir : http://ecolestjeanb.free.fr/hot_potatoes/tutor3.htm

# le code de ce fichier php reste encore à ecrire...

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
/*

Insere un test de closure dans vos articles !
---------------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : [texte], [trou]
separateurs optionnels   : [titre]

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[texte]
	Ceci est un simple test de closure (exercice à trous).
	L'utilisateur entre ses réponses dans les espaces vides, 
	presse ensuite le bouton "Contrôle"
	[trou]
	pour
	[texte]
	découvrir ce qui est correct dans sa réponse et obtenir un score.
	Pour chaque mot manquant, jusqu'à quatre réponses correctes 
	[trou]
	peuvent
	[texte]
	être acceptées. Par exemple, ce  
	[trou]
	trou
	vide
	blanc
	[texte]
	autorise les réponses "trou", "vide" ou "blanc".
</jeux>

*/

function trous_inserer_le_trou($indexTrou, $corrige) {
  global $propositionsTROUS;
  /*
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
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) $codeHTML.="<option value=\"$i\">$valeur</option>";
		$codeHTML.='</select>';
      }
      // Sinon des radio boutons
      else {
		foreach($qcms[$indexQCM]['propositions'] as $i=>$valeur) 
          $codeHTML.='<input type="radio" name="'.$nomVarSelect
		  	. '" value="'.$i.'" id="'.$nomVarSelect.$i.'"><label for="'.$nomVarSelect.$i.'">'
          	. $valeur.'</label><br />';
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
  */
  return " <input name=\"trou$indexTrou\" size=\"9\" onfocus=\"TrackFocus($indexTrou)\" type=\"text\"> ";
}

function trous_inserer_les_trous($chaine, $indexJeux) {
  global $propositionsTROUS;
  if (ereg('<ATTENTE_TROU>([0-9]+)</ATTENTE_TROU>', $chaine, $eregResult)) {
	$indexTROU = intval($eregResult[1]);
	list($texteAvant, $texteApres) = explode($eregResult[0], $chaine, 2); 
	$chaine = $texteAvant.jeux_rem('TROU-DEBUT', $indexTROU)
		. trous_inserer_le_trou($indexTROU, isset($_POST["var_correction_".$indexJeux]))
		. jeux_rem('TROU-FIN', $indexTROU)
		. trous_inserer_les_trous($texteApres, $indexJeux); 
  }
  return $chaine;
}

function jeux_trous($texte, $indexJeux) {
  global $propositionsTROUS;
  $titre = $html = false;
  $indexTrou = 0;

  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('trous', $texte); 
  foreach($tableau as $i => $valeur){
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_TROU) {
		// remplacement des trous par : <ATTENTE_TROU>ii</ATTENTE_TROU>
		$html .= "<ATTENTE_TROU>$indexTrou</ATTENTE_TROU>";
		$propositionsTROUS[$indexTrou] = split("\n", $tableau[$i+1]);
		$indexTrou++;
	  }
  }

   // reinserer les trous mis en forme
  $texte = trous_inserer_les_trous($html, $indexJeux);

  $tete = '<div class="spip_qcm"><div class="spip_qcm_titre">'.$titre.'<hr /></div>';
  if (!isset($_POST["var_correction_".$indexJeux])) { 
	$tete .= "\n".'<form method="post" action="">';
	$pied = '<br>
	<input type="hidden" name="var_correction_'.$indexJeux.'" value="yes">
	<div align="center"><input type="submit" value="'._T('jeux:jeux_corriger').'" class="spip_jeux_bouton_corriger"></div>
	</form>';
  } else {
      // On ajoute le score final
      $pied = '<center><div class="spip_jeux_score">'._T('jeux:jeux_score')
	  			. "&nbsp;$qcm_score&nbsp;/&nbsp;".$qcms['totalscore'].'<br>'
				. ($qcm_score==$qcms['totalscore']?_T('jeux:jeux_bravo'):'').'</div></center>'
				. '<div class="spip_qcm_bouton_corriger" align="right">[ <a href="'
				. parametre_url(self(),'var_mode','recalcul').'">'._T('trous:trous_reinitialiser').'</a> ]</div>';
  }
  
  unset($propositionsTROUS);
  return $texte;
  
  return $tete
    . ($titre?"<span class=\"trous_titre\">$titre</span><br />":'')
	. ($html?"<br /><span class=\"trous_trous\">$html</span>":'')
	. $pied;
}
?>