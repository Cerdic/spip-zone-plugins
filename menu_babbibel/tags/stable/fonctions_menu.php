<?php

function babbi_headeur($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('js/menu_babbi.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('menu_babbi.css').'" type="text/css" media="projection, screen" />';
	return $flux;
}
?>
