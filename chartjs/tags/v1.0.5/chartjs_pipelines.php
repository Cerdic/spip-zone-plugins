<?php
/**
 * Plugin Chart.js bar pour Spip 3.0
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Inserer la CSS de chartjs
 *
 * @param $flux
 * @return mixed
 */
function chartjs_insert_head_css($flux){
  $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path("css/chartjs.css").'" />';
  return $flux;
}

/**
 * Inserer le javascript de chartjs
 *
 * @param $flux
 * @return mixed
 */
function chartjs_insert_head($flux){

  $flux .= 
           "<!--[if lte IE 8]><script src='".find_in_path('js/excanvas.compiled.js')."'></script><![endif]-->\n"
         . "<script type='text/javascript' src='".find_in_path('js/Chart.js')."'></script>"
         . "<script type='text/javascript' src='".find_in_path('js/chart_extra.js')."'></script>";
  
  return $flux;
}



?>