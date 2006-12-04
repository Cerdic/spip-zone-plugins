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
	Ceci est un exemple de closure (exercice à trous).
	L'utilisateur doit entrer ses 
	[trou]
	réponses 
	[texte]
	dans les espaces vides.
	Pour chaque mot manquant, plusieurs réponses correctes 
	peuvent	être acceptées. Par exemple, ce  
	[trou]
	trou, vide, blanc
	[texte]
	autorise les réponses "trou", "vide" ou "blanc".
</jeux>

La liste des mots a placer apres [trou] peut accepter 
les separateurs usuels : 
	retours à la ligne, tabulations, espaces
	virgules, point-virgules, points
Pour une expression comprenant des espaces, utiliser les 
guillemets ou le signe + :
	par ex. : "afin de" est equivalent a : afin+de

*/

function trous_inserer_le_trou($indexJeux, $indexTrou, $size, $corriger) {
  global $propositionsTROUS, $scoreTROUS;

  // Initialisation du code a retourner
  $nomVarSelect = "var{$indexJeux}_T{$indexTrou}";
  $mots = $propositionsTROUS[$indexTrou];
  
  if (!$corriger){
    // affichage sans correction :
     $codeHTML = " <input name=\"trou$indexTrou\" size=\"$size\" onfocus=\"TrackFocus($indexTrou)\" type=\"text\"> "
	 ." (".join('|', $mots).")";
  }

  // Sinon on affiche la correction
  else {
 /*
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
           
	// pas de reponse postee...
	} else $codeHTML.='<div class="qcm_correction_null">'._T('qcm:qcm_reponseNulle').'</div>';
	   
	$codeHTML.='<br />';
*/     
  } //Fin du cas avec correction

  return $codeHTML;
}

function trous_inserer_les_trous($chaine, $indexJeux) {
  global $propositionsTROUS;
  if (ereg('<ATTENTE_TROU>([0-9]+)</ATTENTE_TROU>', $chaine, $eregResult)) {
	$indexTROU = intval($eregResult[1]);
	list($texteAvant, $texteApres) = explode($eregResult[0], $chaine, 2); 
	$texteApres = trous_inserer_les_trous($texteApres, $indexJeux);
	$sizeInput = 0;
	foreach($propositionsTROUS as $trou) foreach($trou as $mot) $sizeInput = max($sizeInput, strlen($mot));
	$chaine = $texteAvant.jeux_rem('TROU-DEBUT', $indexTROU)
		. trous_inserer_le_trou($indexJeux, $indexTROU, $sizeInput, isset($_POST["var_correction_".$indexJeux]))
		. jeux_rem('TROU-FIN', $indexTROU)
		. $texteApres; 
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
		$propositionsTROUS[$indexTrou] = jeux_liste_mots($tableau[$i+1]);
		$indexTrou++;
	  }
  }

   // reinserer les trous mis en forme
  $texte = trous_inserer_les_trous($html, $indexJeux);

  $tete = '<div class="jeux">' . ($titre?'<div class="jeux_titre">'.$titre.'<hr /></div>':'');
  if (!isset($_POST["var_correction_".$indexJeux])) { 
	$tete .= "\n".'<form method="post" action="">';
	$pied = '<br>
	<input type="hidden" name="var_correction_'.$indexJeux.'" value="yes">
	<div align="center"><input type="submit" value="'._T('jeux:corriger').'" class="jeux_bouton_corriger"></div>
	</form>';
  } else {
      // On ajoute le score final
      $pied = '<center><div class="jeux_score">'._T('jeux:jeux_score')
	  			. "&nbsp;$qcm_score&nbsp;/&nbsp;".$qcms['totalscore'].'<br>'
				. ($qcm_score==$qcms['totalscore']?_T('jeux:bravo'):'').'</div></center>'
				. '<div class="jeux_bouton_corriger" align="right">[ <a href="'
				. parametre_url(self(),'var_mode','recalcul').'">'._T('trous:trous_reinitialiser').'</a> ]</div>';
  }
  
  unset($propositionsTROUS);
  
  return $tete.$texte.$pied;
}
?>