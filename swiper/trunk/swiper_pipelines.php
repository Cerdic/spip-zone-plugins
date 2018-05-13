<?php
/**
 * Utilisations de pipelines par Swiper
 *
 * @plugin     Swiper
 * @copyright  2017
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Swiper\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function swiper_configurateur() {

	$c = lire_config('swiper');
	$flux = '<script type="text/javascript">';
	$flux .= '	var swiper_options = '.$c["swiper_options"];
	$flux .= '	, mySwipers={};';
	$flux .= '	$(document).ready(function(){ ';
	$flux .= '    var target = swiper_options.containerModifierClass || ".swiper-container";';
	$flux .= '		target = $(target);';
	$flux .= '		if (target.length) {';
	$flux .= '			target.each(function(i,el) { mySwipers[i] = new Swiper($(el), swiper_options); })';
	$flux .= '		}';
	$flux .= '	})';
	$flux .= '</script>';

	return $flux;

}

function swiper_insert_head($flux) {

	$lib 	= find_in_path('lib/Swiper/dist/js/swiper.min.js');
	$flux .='<script src="'.$lib.'"	type="text/javascript"></script>';
	$flux .= swiper_configurateur();

	return $flux;
}

function swiper_insert_head_css($flux) {

	$css = find_in_path('lib/Swiper/dist/css/swiper.min.css');
	$flux .='<link rel="stylesheet" type="text/css" href="'.$css.'">';

	$swiper_spip_css = find_in_path('swiper_spip.css');
	$flux .='<link rel="stylesheet" type="text/css" href="'.$swiper_spip_css.'">';

	return $flux;
}

function swiper_header_prive($flux) {

	$css = find_in_path('lib/Swiper/dist/css/swiper.min.css');
	$flux .='<link rel="stylesheet" type="text/css" href="'.$css.'">';

	$swiper_spip_css = find_in_path('swiper_spip.css');
	$flux .='<link rel="stylesheet" type="text/css" href="'.$swiper_spip_css.'">';

	$lib 	= find_in_path('lib/Swiper/dist/js/swiper.min.js');
	$flux .='<script src="'.$lib.'"	type="text/javascript"></script>';

	$flux .= swiper_configurateur();

	return $flux;
}
