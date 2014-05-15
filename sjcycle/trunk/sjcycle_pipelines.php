<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// include_spip('inc/config');

function sjcycle_insert_head_css($flux){
	$flux .= "\n".'<link rel="stylesheet" type="text/css" href="'.find_in_path('css/sjcycle.css').'" media="all" />'."\n";
	return $flux;
}

function sjcycle_insert_head($flux){

	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.flip.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.carousel.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.scrollVert.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.shuffle.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.tile.js').'" type="text/javascript"></script>';
	
	return $flux;
}

?>