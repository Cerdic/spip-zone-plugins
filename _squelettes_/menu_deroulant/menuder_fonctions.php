<?php

function menuder_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('menu_deroulant.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('menu_deroulant.css').'" type="text/css" media="projection, screen" />';
	return $flux;
}
?>
