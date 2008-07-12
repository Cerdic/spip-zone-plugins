<?php
/**
 * Classe acsAudio. Insère les javascripts et le swf
 * L'ordre de chargement des scripts est impératif
 */

class acsAudio extends Composant{
  public function insert_head($flux) {
    $flux .= '<script type="text/javascript">var acsAudio_img_pack = "'._DIR_PLUGIN_ACS.'models/'.$GLOBALS['meta']['acsModel'].'/composants/audio/img_pack/";</script>';
    $flux .= '<script type="text/javascript" src="'.find_in_path('composants/audio/js/soundmanager2-jsmin.js').'"></script>';
    $flux .= '<script type="text/javascript">soundManager.debugMode = false;</script>';
    $flux .= '<script type="text/javascript" src="'.find_in_path('composants/audio/js/audio.js').'"></script>';
    $flux .= '<script type="text/javascript">soundManager.createMovie("'.url_absolue(find_in_path('composants/audio/swf/soundmanager2.swf')).'");</script>';
    return $flux;
  }
}

?>