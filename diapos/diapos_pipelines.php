<?php
function diapos_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('diapos.js').'"></script>';
	$flux .= '<link rel="stylesheet" href="'.find_in_path('diapos.css').'" type="text/css" media="all" />';
	return $flux;
}
function diapos_header_prive($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('diapos.js').'"></script>';
	$flux .= '<link rel="stylesheet" href="'.find_in_path('diapos.css').'" type="text/css" media="all" />';
	return $flux;
}
?>