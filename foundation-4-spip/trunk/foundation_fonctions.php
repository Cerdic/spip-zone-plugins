<?php
/**
 * Fonction d'upgrade/installation du plugin foundation-4-spip
 *
 * @plugin     foundation-4-spip
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction de callback
 * A chaque iFrame, on encadre de div.flex-video.
 * Si vimeo est détecté, on ajoute la class vimeo
 * @param  string $matches iframe
 * @return string          iframe encadrée
 */
function responsive($matches) {
  // Dans le cas de vimeo, il faut ajouter une classe
  if (strpos($matches[0], 'vimeo')) $vimeo = ' vimeo';
  else $vimeo = '';

  // On revoie la bonne structure html d'iframe.
  return '<div class="flex-video'.$vimeo.'"><iframe '.$matches[0].'></iframe></div>';
}

/**
 * Rendre les iframes responsive via un filtre et la classe flex-video de Foundation.
 * @param  string $texte HTML pouvant contenir des iFrames
 * @return string        HTML avec les iFrames modifiée pour être responsive.
 */
function iframe_responsive($texte) {
  // On détecte tout les iFrames et on les rends responsives.
  return preg_replace_callback('/<iframe(.+)><\/iframe>/', 'responsive', $texte);
}

/**
 * Pas très élégant, cette fonction va renvoyer le nombre en toutes lettre.
 *  Dans ce cas si, cela suffit largement puisqu'il n'y a que 12 chiffres possible.
 *  C'est utiliser pour foundation 2 et 3.
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


/**
 * Cette fonction va lire la configuration de foundation et determiner quel syntaxe doit être utilisé.
 * @param  int $nombre_colonnes Nombre de colonne désiré
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

  // on cherche
  // Si on est dans une vesion numérique de foundation, on retourne la syntaxe
  if (in_array($config['variante'], $colnum))
    return $type.'-'.$nombre_colonnes;
  // Sinon, on démarrer le moteur de conversion de nombre, et on renvoie la bonne class
  elseif (in_array($config['variante'], $colettr))
    return toWords($nombre_colonnes);
}

/*
*   Cette balise va permettre de rendre le squelette compatible avec toutes les versions de Foundation.
*   La syntaxe est la suivante:
*
*   #COLONNES{nombre,type}
*   nombre: le nombre de colonne foundation
*   (optionnel) type: Dans le cas des version utilisant une syntaxe avec prefix, on lui passe le type (défaut: large)
*/
function balise_COLONNES_dist($p) {
  // On récupère les paramètres de la balise.
  $nombre_colonnes = interprete_argument_balise(1, $p);
  $type = interprete_argument_balise(2, $p);

  // On met une valeur par défaut à type.
  if (!$type) $type = 'large';

  $p->code = "trouver_syntaxe_foundation($nombre_colonnes, $type).' columns'";
  $p->interdire_scripts = false;
  return $p;
}