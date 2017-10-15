<?php

function simplec_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/simplec.css').'" type="text/css" media="all" />';
	$flux .= '<script type="text/javascript" src="'.find_in_path('js/clipboard.min.js').'"></script>';
	$flux .= '<script type="text/javascript" src="'.find_in_path('js/simplec.js').'"></script>';
	return $flux;
}

