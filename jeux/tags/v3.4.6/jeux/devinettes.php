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

Insere des devinettes ou charades dans vos articles !
-----------------------------------------------------

separateurs obligatoires : [devinette] ou [charade]
separateurs optionnels   : [reponse], [titre], [texte], [config]
parametres de configurations par defaut :
	reponse=oui	// afficher la reponse ?
	envers=oui // retourner la reponse ?
	taille=10	// taille de la police utilisee
attention : module GD obligatoire pour obtenir ses reponses
affichees a l'envers.

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[devinette]
	Comment appelle t-on un patron de la nouvelle economie ?
	[reponse]
	Un e-responsable
	[devinette]
	Quel est le point commun entre un controleur des impots et un spermatozoïde ?
	[reponse]
	Tous les 2 ont 1 chance sur 3 millions de devenir un jour un etre humain.
</jeux>
<jeux>
	[titre]
	Pour les enfants...
	[charade]
	Mon premier se dirige quelque part.
	Mon deuxieme est la moitie d'un cheveux.
	Mon tout vit a la ferme.
	[reponse]
	La vache
	[config]
	reponse = non
</jeux>

*/

// fonctions d'affichage
function devinettes_titre($texte) {
 return $texte?"<div class=\"jeux_titre devinettes_titre\">$texte</div>":'';
}
function devinettes_devinette($texte) {
 return $texte?"<div class=\"jeux_question devinettes_devinette\">$texte</div>":'';
}
function devinettes_charade($texte) {
 $texte = "<poesie>$texte</poesie>";
 return $texte?"<div class=\"jeux_question devinettes_charade\">$texte</div>":'';
}
function devinettes_reponse($texte, $id) {
 if (!jeux_config('reponse')) return '';
 include_spip('inc/filtrer');
 include_spip('images_fonctions'); // image_typo pour SPIP 2.1
 $image = image_typo($texte, 'taille='.jeux_config('taille'));
 if (jeux_config('envers'))
 	$image = filtrer('image_flip_vertical', filtrer('image_flip_horizontal', $image));
 if (function_exists('image_graver')) $image = filtrer('image_graver', $image);
 $image = inserer_attribut($image, 'align', 'right', false, true);
 $image = aligner_droite(inserer_attribut($image, 'class', 'no_image_filtrer', false, true));
 $texte = jeux_block_depliable(_T('jeux:reponse'), $image);
 return $texte?"<div class=\"devinettes_reponse\">$texte</div>":'';
}

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_devinettes_init() {
	return "
		reponse=oui	// afficher la reponse ?
		envers=oui // retourner la reponse ?
		taille=10	// taille de la police utilisee
	";
}

// fonction principale, pas de formulaire
function jeux_devinettes($texte, $indexJeux, $form=true) {
  $html = false;
  
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('devinettes', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $html .= devinettes_titre($tableau[$i+1]);
	  elseif ($valeur==_JEUX_DEVINETTE) $html .= devinettes_devinette($tableau[$i+1]);
	  elseif ($valeur==_JEUX_CHARADE) $html .= devinettes_charade($tableau[$i+1]);
	  elseif ($valeur==_JEUX_REPONSE) $html .= devinettes_reponse($tableau[$i+1], "devinettes_{$indexJeux}_$i");
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
  }
  return $html;
}

?>