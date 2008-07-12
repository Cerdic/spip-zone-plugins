<?php

/**
 * Classe acsBreves. Insère les javascripts
 */

class acsBreves extends Composant{
  public function insert_head($flux) {
    $flux .= '<script type="text/javascript" src="'.find_in_path('composants/breves/js/breves.js').'"></script>';
    return $flux;
  }
}
?>