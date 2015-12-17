<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de callback utiliser par le filtre |iframe_responsive
 * A chaque iFrame, on encadre de div.flex-video.
 * Si vimeo est détecté, on ajoute la class vimeo
 * @param  string $matches iframe
 * @return string          iframe encadrée
 */
function responsive($matches) {
  // Dans le cas de vimeo, il faut ajouter une classe
  if (strpos($matches[0], 'vimeo')) $vimeo = ' vimeo';
  else $vimeo = '';

  // On inclu les filtres, au cas ou
  include_spip('inc/filtres');

  // On revoie la bonne structure html d'iframe.
  return wrap($matches[0], '<div class="flex-video'.$vimeo.'">');;
}
