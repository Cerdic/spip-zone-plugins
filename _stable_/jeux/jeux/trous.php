<?php

# il s'agit ici de proposer des textes a trous.

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

Insere un test de closure dans vos articles !
---------------------------------------------

separateurs obligatoires : [texte], [trou]
separateurs optionnels   : [titre], [config]
parametres de configurations par defaut :
	taille=auto	// taille des trous
	indices=oui	// afficher les indices ?

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[texte]
	Ceci est un exemple de closure (exercice a trous).
	L'utilisateur doit entrer ses 
	[trou]
	reponses 
	[texte]
	dans les espaces vides.
	Pour chaque mot manquant, plusieurs reponses correctes 
	peuvent	etre acceptees. Par exemple, ce  
	[trou]
	trou, vide, blanc
	[texte]
	autorise les reponses "trou", "vide" ou "blanc".
	[config]
	indices = oui
</jeux>

La liste des mots a placer apres [trou] peut accepter 
les separateurs usuels : 
	retours a la ligne, tabulations, espaces
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
  $prop = strtolower($_POST[$nomVarSelect] = trim($_POST[$nomVarSelect]));						  // function TrackFocus(BoxNumber){CurrentWord = BoxNumber;}
  $codeHTML = " <input name=\"$nomVarSelect\" class=\"jeux_input\" size=\"$size\"  type=\"text\"" //onfocus=\"TrackFocus('$nomVarSelect')\"
	  . ($prop?" value=\"{$_POST[$nomVarSelect]}\"":'') . "> "
	 ;// . " (".join('|', $mots).")";

  // en cas de correction
  if ($corriger){
   if ($prop!='' && in_array($prop, $mots)) ++$scoreTROUS;
  }
  return $codeHTML;
}

function trous_inserer_les_trous($chaine, $indexJeux) {
  global $propositionsTROUS;
  if (ereg('<ATTENTE_TROU>([0-9]+)</ATTENTE_TROU>', $chaine, $eregResult)) {
	$indexTROU = intval($eregResult[1]);
	list($texteAvant, $texteApres) = explode($eregResult[0], $chaine, 2); 
	$texteApres = trous_inserer_les_trous($texteApres, $indexJeux);
	if (($sizeInput = intval(jeux_config('taille')))==0)
		foreach($propositionsTROUS as $trou) foreach($trou as $mot) $sizeInput = max($sizeInput, strlen($mot));
	$chaine = $texteAvant.jeux_rem('TROU-DEBUT', $indexTROU)
		. trous_inserer_le_trou($indexJeux, $indexTROU, $sizeInput, isset($_POST["var_correction_".$indexJeux]))
		. jeux_rem('TROU-FIN', $indexTROU)
		. $texteApres; 
  }
  return $chaine;
}

// afficher l'ensemble des solutions dans le desordre...
// si plusieurs solutions sont possibles, seule la premiere est retenue
function trous_afficher_indices($indexJeux) {
 global $propositionsTROUS;
 foreach ($propositionsTROUS as $prop) $indices[] = $prop[0];
 shuffle($indices);
 return '<br/>'.jeux_block_invisible('trous_indices_'.$indexJeux, _T('jeux:indices'), '<center>'.join(' -&nbsp;', $indices).'</center>');
}

function jeux_trous($texte, $indexJeux) {
  global $propositionsTROUS, $scoreTROUS;
  $titre = $html = false;
  $indexTrou = $scoreTROUS = 0;
  jeux_block_init();

  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('trous', $texte); 
  // configuration par defaut
  jeux_config_init("
	taille=auto	// taille des trous
	indices=oui	// afficher les indices ?
  ", false);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_TROU) {
		// remplacement des trous par : <ATTENTE_TROU>ii</ATTENTE_TROU>
		$html .= "<ATTENTE_TROU>$indexTrou</ATTENTE_TROU>";
		$propositionsTROUS[$indexTrou] = jeux_liste_mots_min($tableau[$i+1]);
		$indexTrou++;
	  }
  }

   // reinserer les trous mis en forme
  $texte = trous_inserer_les_trous($html, $indexJeux);

  $tete = '<div class="jeux_cadre">' . ($titre?'<div class="jeux_titre">'.$titre.'<hr /></div>':'');
  $pied = jeux_config('indices')?trous_afficher_indices($indexJeux):'';

  if (!isset($_POST["var_correction_".$indexJeux])) { 
	$tete .= jeux_form_debut('trous', $indexJeux);
	$pied .= '<br /><div align="center"><input type="submit" value="'._T('jeux:corriger').'" class="jeux_bouton"></div>'.jeux_form_fin();
  } else {
      // On ajoute le score final
        $pied .= jeux_afficher_score($scoreTROUS, $indexTrou, $_POST['id_jeu'])
  			. jeux_bouton_recommencer();
  }
  
  unset($propositionsTROUS); unset($scoreTROUS);
  return $tete.$texte.$pied.'</div>';
}
?>