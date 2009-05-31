<?php
/*
// méthode devenue obsolete, source de trop de bogues
if (false) {//(!function_exists("introduction")) {	

  function introduction ($type, $texte, $chapo='', $descriptif='') {

	switch ($type) {
		case 'articles':
			if ($descriptif)
				return propre($descriptif);
			else if (substr($chapo, 0, 1) == '=')	// article virtuel
				return '';
			else
				return PtoBR(propre(supprimer_tags(couper_intro(pas_de_grille_introduction($chapo."\n\n\n".$texte), 500))));
			break;
		case 'breves':
			return PtoBR(propre(supprimer_tags(couper_intro(pas_de_grille_introduction($texte), 300))));
			break;
		case 'forums':
			return PtoBR(propre(supprimer_tags(couper_intro(pas_de_grille_introduction($texte), 600))));
			break;
		case 'rubriques':
			if ($descriptif)
				return propre(pas_de_grille_introduction($descriptif));
			else
				return PtoBR(propre(supprimer_tags(couper_intro(pas_de_grille_introduction($texte), 600))));
			break;
	}
	
  }  // function introduction
}	// if exists

function pas_de_grille_introduction($texte) {
	$texte = preg_replace(',<(grille)>(.*)<\/\1>,UimsS','',$texte);
	$texte = preg_replace(',<(horizontal)>(.*)<\/\1>,UimsS','',$texte);
	$texte = preg_replace(',<(vertical)>(.*)<\/\1>,UimsS','',$texte);
	return $texte;
}
*/
?>