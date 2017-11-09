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
	# Todo: moment.js est nécessaire pour gérer correctement les graphiques avec des dates.
	#$flux .=  "<script type='text/javascript' src='" . find_in_path('lib/moment/moment-with-locales.min.js') . "'></script>";
	$flux .=  "<script type='text/javascript' src='" . find_in_path('lib/chartjs/Chart.js') . "'></script>";
	return $flux;
}