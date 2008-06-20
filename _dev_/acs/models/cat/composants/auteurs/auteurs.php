<?php

/**
 * Classe acsAuteurs. Insère les javascripts
 */

class acsAuteurs extends Composant{
  public function insert_head($flux) {
    $flux .= '<script type="text/javascript" src="'.find_in_path('composants/auteurs/js/auteurs.js').'"></script>';
    return $flux;
  }
}
?>