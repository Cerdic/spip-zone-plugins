<?php

function menuder_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('js/menuder.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/menuder.css').'" type="text/css" media="projection, screen" />';
	return $flux;
}
?>
