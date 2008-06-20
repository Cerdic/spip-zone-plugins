<?php
/**
 * Classe acsAgenda. Insère les javascripts
 */

class acsAgenda extends Composant{
  public function insert_head($flux) {
    $flux .= '<script type="text/javascript" src="'.find_in_path('composants/agenda/js/agenda.js').'"></script>';
    return $flux;
  }
}
?>