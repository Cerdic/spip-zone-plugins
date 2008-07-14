<?php
/**
 * Classe acsForums. Insère les javascripts
 */

class acsForums extends Composant{
  public function insert_head($flux) {
    $flux .= '<script type="text/javascript" src="'.find_in_path('composants/forums/js/forums.js').'"></script>';
    return $flux;
  }
}
?>