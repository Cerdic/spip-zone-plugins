<?php
/* Plugin timecircles (SPIP 3.1+)
 * (c) 2009-2018 Wim Barelds
 * packaged for SPIP by Loiseau2nuit
 *
 * Add beautiful jquery powered timers to your 
 * website with simple short models
 * 
 * Licence: MIT
 * https://opensource.org/licenses/mit-license.php 
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * load timecircles' css
 **/
function timecircles_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" type="text/css" media="all" href="'.find_in_path('css/timecircles.css').'" />';
	return $flux;
}

/**
 * load timecircles' js in the admin area
 **/
function timecircles_header_prive($flux){
	$flux = timecircles_insert_head_css($flux);
	$flux = timecircles_insert_head($flux);
	return $flux;
} 

/**
 * load timecircles' js on the website
 **/
function timecircles_insert_head($flux){
	$flux .= '<script src="'.generer_url_public('lib/timecircles.js').'" type="text/javascript"></script>'
	. '<script src="'.find_in_path('js/timecircles.js').'" type="text/javascript"></script>';
	return $flux;
}
