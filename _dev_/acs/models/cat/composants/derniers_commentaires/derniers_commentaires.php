<?php

/**
 * Classe acsDerniers_commentaires. Insère les javascripts
 */

class acsDerniers_commentaires extends Composant{
  public function insert_head($flux) {
    $flux .= '<script type="text/javascript" src="'.find_in_path('composants/derniers_commentaires/js/derniers_commentaires.js').'"></script>';
    return $flux;
  }
}
?>