<?php

/**
 * definition du plugin "rien" version "classe statique"
 */
class TriMots {
  
  function ajouter_boite_gauche($arguments) {
	if($arguments['args'] == 'articles') {
	  return $arguments['data'] .= TriMots::boite_tri_mots();
	}
    return $arguments['data'];
  }
  
  function boite_tri_mots() {
	$to_ret = '<div>&nbsp;</div>';
	$to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
	$to_ret .= bandeau_titre_boite2('Tri Mots',"article-24.gif","white","black", false);
	$to_ret .= '<div class="plan-articles">';
	$to_ret .= 'TRI';
	$to_ret .= '</div></div>';
	return $to_ret;
  }
}
?>