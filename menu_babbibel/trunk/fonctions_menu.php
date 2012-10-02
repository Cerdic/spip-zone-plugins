<?php

function babbi_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.find_in_path('menu_babbi.css').'" type="text/css" media="projection, screen, tv" />';
	}
	return $flux;
}

function babbi_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('js/menu_babbi.js')."'></script>\n";
	$flux .= babbi_insert_head_css(''); // compat pour les vieux spip
	return $flux;
}
?>