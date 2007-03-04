<?php

function menuaccordeon_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('menu_accordeon.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('menu_accordeon.css').'" type="text/css" media="projection, screen" />';
	$flux .= "<script type='text/javascript' src='".find_in_path('menu_accordeon_call.js')."'></script>\n";
	return $flux;
}
?>
