<?php 

function mot_croises_header_prive($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('mots-croises-prive.css')).'" />';
	$flux .='<script type="text/javascript" src="'.find_in_path("mots-croises.js").'"></script>';
	return $flux;
}
?>