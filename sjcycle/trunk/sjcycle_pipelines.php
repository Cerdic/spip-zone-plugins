<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// include_spip('inc/config');

function sjcycle_insert_head_css($flux){
	$conf_sjcycle = lire_config('sjcycle');

	if($conf_sjcycle['tooltip']) {
		$flux .="\n".'<link rel="stylesheet" href="'.find_in_path('lib/jquery.tooltip.css').'" type="text/css" media="all" />';
	}
	$flux .= "\n".'<link rel="stylesheet" type="text/css" href="'.find_in_path('css/sjcycle.css').'" media="all" />'."\n";
	return $flux;
}

function sjcycle_insert_head($flux){
	$conf_sjcycle = lire_config('sjcycle');

	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.flip.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.carousel.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.scrollVert.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.shuffle.js').'" type="text/javascript"></script>';
	$flux .="\n".'<script src="'.find_in_path('lib/jquery.cycle2.tile.js').'" type="text/javascript"></script>';
	if($conf_sjcycle['tooltip']) {
		$flux .="\n".'<script src="'.find_in_path('lib/jquery.tooltip.js').'" type="text/javascript" charset="utf-8"></script>';
	}
	
	return $flux;
}