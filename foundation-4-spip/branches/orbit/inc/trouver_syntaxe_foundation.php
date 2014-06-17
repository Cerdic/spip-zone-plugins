<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette fonction va lire la configuration de foundation et determiner quel syntaxe doit être utilisé.
 * @param  int|array $nombre_colonnes Nombre de colonne désiré
 * @param  string $type            Foundation 4/5, type de colonne (large, medium, small)
 * @return string                  class foundation applicable directement.
 */
function trouver_syntaxe_foundation($nombre_colonnes, $type) {

  // On récupère la configuration
  $config = lire_config('foundation');

  // Version qui utilise un système large-X ou small-X. J'appel ce groupe les colnum.
  $colnum = array(4,5);

  // Les versions qui utilise des lettres => les colletr
  $colettr = array(2,3);

  // Si la première variable est un tableau, on va le convertir en class
  // On limite ce système a foundation >= 4
  if (is_array($nombre_colonnes)
      and in_array($config['variante'], $colnum)) {
    $class= '';
    foreach ($nombre_colonnes as $key => $value) {
      $class .= $key.'-'.$value.' ';
    }
    return $class;
  }
  else {
    // Si on est dans une vesion numérique de foundation, on retourne la syntaxe
    if (in_array($config['variante'], $colnum))
      return $type.'-'.$nombre_colonnes;
    // Sinon, on démarrer le moteur de conversion de nombre, et on renvoie la bonne class
    elseif (in_array($config['variante'], $colettr)) {
      return toWords($nombre_colonnes);
    }
  }
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