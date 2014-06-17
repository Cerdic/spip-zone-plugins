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
  return wrap('<iframe '.$matches[0].'></iframe>', '<div class="flex-video'.$vimeo.'">');;
}

/**
 * Pas très élégant, cette fonction va renvoyer le nombre en toutes lettre.
 * Dans ce cas si, cela suffit largement puisqu'il n'y a que 12 chiffres possible.
 * C'est utiliser pour foundation 2 et 3.
 * @param  int $number nombre de colonne foundation.
 * @return string         string définissant la largeur de la colonne foundation.
 */
function toWords($number) {

  // Ce tableau fait office de table de conversion.
  $conversion = array(
                      1 => 'one',
                      2 => 'two',
                      3 => 'three',
                      4 => 'four',
                      5 => 'five',
                      6 => 'six',
                      7 => 'seven',
                      8 => 'eight',
                      9 => 'nine',
                      11 => 'eleven',
                      12 => 'twelve'
                      );

    // On revoie la bonne valeur.
  if (is_numeric($number))
    return $conversion[$number];
  else return false;
}