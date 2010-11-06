<?php
# ACS
# Copyright Daniel FAIVRE 2008-2010 - Copyleft licence GPL
#
/**
 * La classe acsOngletsEdit étend CEdit,
 * et implémente sa méthode update() pour personnaliser
 * les images d'onglets "portes coulissantes CSS".
 */
class acsOngletsEdit extends CEdit {
  function update() {
  $bc = metacol('acsOngletsBordColor');
  $ac = metacol('acsOngletsFondColor');
  $ic = metacol('acsOngletsCouleurInactif');
  $hc = metacol('acsOngletsCouleurSurvol');

    $imr = @ImageCreateFromGif(find_in_path('composants/onglets/images/right.gif'));
    $iml = @ImageCreateFromGif(find_in_path('composants/onglets/images/left.gif'));

    if (!$imr || !$iml) {
    	$this->errors[] = 'ImageCreateFromGif_fail';
    	return false;
    }

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

    $dir_img = '../'.$GLOBALS['ACS_CHEMIN'].'/onglets';
    if (!is_readable($dir_img)) mkdir_recursive($dir_img);
    if ( (!@imagegif($imr,$dir_img.'/right.gif')) ||
    	(!@imagegif($iml,$dir_img.'/left.gif')) ) {
  			$this->errors[] = 'unable_to_create_imagegif in '.$dir_img;
      	return false;
    	}
    return true;
  }
}

function metacol($var) {
  $r = substr($GLOBALS['meta'][$var],1);
  //spip_log( $r . ' ');
  return $r;
}
?>