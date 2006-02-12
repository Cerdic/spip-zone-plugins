<?php

/**
 * definition du plugin "rien" version "classe statique"
 */
class TriMots {
  
  function ajouter_boite_gauche($arguments) {
	if($arguments['args'] == 'article') {
	  return $arguments['data'] .= boite_tri_mots();
	}
    return $arguments['data'];
  }
  
  function boite_tri_mots() {
	return "Boite Gauche";
  }
}
?>
