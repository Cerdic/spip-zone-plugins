<?php

function fragahah_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('pagination-ahah.css').'" type="text/css" media="projection, screen" />';
	$flux .= "<script type='text/javascript' src='".find_in_path('pagination-ahah.js')."'></script>\n";
	return $flux;
}


?>