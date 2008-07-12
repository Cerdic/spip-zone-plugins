<?php

/**
 * Classe acsPlaylist. Insère les javascripts
 */

class acsPlaylist extends Composant{
  public function insert_head($flux) {
    $flux .= '<script type="text/javascript" src="'.find_in_path('composants/playlist/js/playlist.js').'"></script>';
    return $flux;
  }
}
?>