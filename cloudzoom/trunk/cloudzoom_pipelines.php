<?php
/**
 * Plugin CloudZoom
 * Distribue sous licence GPL
 *
 * @package SPIP\CloudZoom\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")){
	return;
}

/**
 * CSS
 * @param string $flux
 * @return string
 */
function cloudzoom_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="' . timestamp(find_in_path('css/cloudzoom.css')) . '" media="all" />' . "\n";
	return $flux;

}

/**
 * JS
 * @param string $flux
 * @return string
 */
function cloudzoom_insert_head($flux){
	$js = timestamp(find_in_path('js/cloud-zoom.1.0.3.min.js'));
	$flux .= "<script type='text/javascript' src='$js'></script>\n";

	return $flux;

}








