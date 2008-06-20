<?php

/**
 * ACS
 * Copyright Daniel FAIVRE 2008 - Copyleft licence GPL
 *
 * Implémentation d'une classe acsOnglets qui étend Composant,
 * avec sa méthode afterUpdate() pour personnaliser
 * les images d'onglets "portes coulissantes CSS".
 */

class acsOnglets extends Composant {
  function afterUpdate() {
    $bc = $GLOBALS['meta']['acsOngletsBordColor'];
    $ac = $GLOBALS['meta']['acsOngletsFondColor'];
    $ic = $GLOBALS['meta']['acsOngletsCouleurInactif'];
    $hc = $GLOBALS['meta']['acsOngletsCouleurSurvol'];

    $imr = @ImageCreateFromGif(find_in_path('composants/onglets/img_pack/right.gif'));
    $iml = @ImageCreateFromGif(find_in_path('composants/onglets/img_pack/left.gif'));

    if (!$imr || !$iml) return false;

    // bord
    $br = hexdec(substr($bc,0,2));
    $bg = hexdec(substr($bc,2,2));
    $bb = hexdec(substr($bc,4,2));

    // actif (fond)
    $ar = hexdec(substr($ac,0,2));
    $ag = hexdec(substr($ac,2,2));
    $ab = hexdec(substr($ac,4,2));

    // inactif
    $ir = hexdec(substr($ic,0,2));
    $ig = hexdec(substr($ic,2,2));
    $ib = hexdec(substr($ic,4,2));

    // hover
    $hr = hexdec(substr($hc,0,2));
    $hg = hexdec(substr($hc,2,2));
    $hb = hexdec(substr($hc,4,2));

    imagecolorset($imr, 0, $br, $bg, $bb);
    imagecolorset($imr, 1, $ir, $ig, $ib);
    imagecolorset($imr, 2, $hr, $hg, $hb);
    imagecolorset($imr, 3, $ar, $ag, $ab);

    imagecolorset($iml, 0, $br, $bg, $bb);
    imagecolorset($iml, 1, $ir, $ig, $ib);
    imagecolorset($iml, 2, $hr, $hg, $hb);
    imagecolorset($iml, 3, $ar, $ag, $ab);

    $dir_img = '../'.$GLOBALS['ACS_CHEMIN'].'/img/onglets';
    if (!is_readable($dir_img)) mkdir_recursive($dir_img);
    if (!@imagegif($imr,'../'.$GLOBALS['ACS_CHEMIN'].'/img/onglets/right.gif')) return false;
    if (!@imagegif($iml,'../'.$GLOBALS['ACS_CHEMIN'].'/img/onglets/left.gif')) return false;

    return true;
  }
}

?>