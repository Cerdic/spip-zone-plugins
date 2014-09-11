<?php
/**
 * Plugin Cookie bar pour Spip 3.0
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Inserer la CSS de cookie bar
 *
 * @param $flux
 * @return mixed
 */
function cookiebar_insert_head_css($flux){
  $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path("css/jquery.cookiebar.css").'" />';
  return $flux;
}

/**
 * Inserer le javascript de cookiebar
 *
 * @param $flux
 * @return mixed
 */
function cookiebar_insert_head($flux){
  $js_cookiebar = parametre_url(generer_url_public('jquery.cookiebar.js'), 'lang', $lang);

  //$flux .= '<script type='text/javascript' src="'.find_in_path('js/jquery.cookiebar.js').'"></script>';
  $flux .= 
           "<script type='text/javascript' src='$js_cookiebar'></script>\n"
         . "<script type='text/javascript' src='".find_in_path('js/jquery.cookiebar.call.js')."'></script>";
  
  return $flux;
}

    /*
    	$markitup = find_in_path('javascript/jquery.markitup_pour_spip.js');
	$js_previsu = find_in_path('javascript/jquery.previsu_spip.js');
	$js_start = parametre_url(generer_url_public('porte_plume_start.js'), 'lang', $lang);
	if (defined('_VAR_MODE') AND _VAR_MODE=="recalcul")
		$js_start = parametre_url($js_start, 'var_mode', 'recalcul');

	$flux .= 
		   "<script type='text/javascript' src='$markitup'></script>\n"
		.  "<script type='text/javascript' src='$js_previsu'></script>\n"
		.  "<script type='text/javascript' src='$js_start'></script>\n";
     *
	return $flux;
      */


?>
