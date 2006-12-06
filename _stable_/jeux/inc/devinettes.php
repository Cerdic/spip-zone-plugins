<?php

# le code de ce fichier php reste encore à ecrire...

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
/*

Insere des devinettes ou charades dans vos articles !
-----------------------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : [devinette] ou [charade]
separateurs optionnels   : [reponse], [titre], [texte], [config]
attention : module GD obligatoire pour obtenir ses reponses
affichees a l'envers.

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[devinette]
	Comment appelle t-on un patron de la nouvelle économie ?
	[reponse]
	Un e-responsable
	[devinette]
	Quel est le point commun entre un contrôleur des impôts et un spermatozoïde ?
	[reponse]
	Tous les 2 ont 1 chance sur 3 millions de devenir un jour un être humain.
	[config]
	reponse = oui
</jeux>
<jeux>
	[titre]
	Pour les enfants...
	[charade]
	Mon premier se dirige quelque part.
	Mon deuxième est la moitié d'un cheveux.
	Mon tout vit à la ferme.
	[reponse]
	La vache
</jeux>

*/

// fonctions d'affichage
function devinettes_titre($texte) {
 return $texte?"<p class=\"jeux_titre devinettes_titre\">$texte</p>":'';
}
function devinettes_devinette($texte) {
 return $texte?"<p class=\"jeux_question devinettes_devinette\">$texte</p>":'';
}
function devinettes_charade($texte) {
 $texte = "<poesie>$texte</poesie>";
 return $texte?"<p class=\"jeux_question devinettes_charade\">$texte</p>":'';
}
function devinettes_reponse($texte, $id) {
 if (!jeux_config('reponse')) return '';
 include_spip('inc/filtrer');
 $image = image_typo($texte, 'taille=10');
 $image = aligner_droite(filtrer('image_flip_vertical', filtrer('image_flip_horizontal', $image)));
 $texte = jeux_block_invisible($id, _T('jeux:reponse'), $image);
 return $texte?"<span class=\"devinettes_reponse\">$texte</span>":'';
}

// fonction principale 
function jeux_devinettes($texte, $indexJeux) {
  $html = false;
  jeux_block_init();
  
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('devinettes', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $html .= devinettes_titre($tableau[$i+1]);
	  elseif ($valeur==_JEUX_DEVINETTE) $html .= devinettes_devinette($tableau[$i+1]);
	  elseif ($valeur==_JEUX_CHARADE) $html .= devinettes_charade($tableau[$i+1]);
	  elseif ($valeur==_JEUX_REPONSE) $html .= devinettes_reponse($tableau[$i+1], "devinettes_$indexJeux_$i");
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
  }
  return $html;
}

?>