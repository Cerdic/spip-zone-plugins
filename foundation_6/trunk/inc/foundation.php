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
  if (strpos($matches[0], 'vimeo')) {
	  $vimeo = ' vimeo';
  }
  else {
	  $vimeo = '';
  }

  // On inclu les filtres, au cas ou
  include_spip('inc/filtres');

  // On revoie la bonne structure html d'iframe.
  return wrap($matches[0], '<div class="flex-video'.$vimeo.'">');;
}

/**
 * Cette fonction va créer la class foundation de la balise #COLONNE
 *
 * @param  int|array $nombre_colonnes Nombre de colonne désiré
 * @param  string $type Foundation 4/5, type de colonne (large, medium, small)
 * @return string class foundation applicable directement.
 */
function class_grid_foundation($nombre_colonnes, $type) {

    // Si la première variable est un tableau, on va le convertir en class
    if (is_array($nombre_colonnes)) {
        $class= '';
        foreach ($nombre_colonnes as $key => $value) {
            // Utiliser un tableau large => 4
            if (is_numeric($value)) {
                $class .= $key.'-'.$value.' ';
            }
        }
        return $class;
    }
    else {
        return $type.'-'.$nombre_colonnes.' ';
    }
}
