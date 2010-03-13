<?php
function diapos_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('diapos.js').'"></script>';
	return $flux;
}
function diapos_header_prive($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('diapos.js').'"></script>';
	return $flux;
}
?>