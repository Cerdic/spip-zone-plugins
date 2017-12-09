<?php
/*
 * Plugin Webfonts2
 * (c) 2016
 * Distribue sous licence GPL
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function googlefont_request($webfonts){
	foreach($webfonts as $font){
		$variants = implode(',',$font['variants']);
		$fonts[] = urlencode($font['family']).':'.$variants;	
	}	
	$fonts = implode('|',$fonts);
	$request = "https://fonts.googleapis.com/css?family=$fonts";
	return $request;
}