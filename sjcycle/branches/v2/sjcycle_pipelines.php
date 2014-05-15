<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');

function sjcycle_insert_head_css($flux){
	$conf_jcycle = lire_config('sjcycle');
	if($conf_jcycle["tooltip"]) {
		$flux .="\n".'<link rel="stylesheet" href="'.find_in_path('javascript/jquery.tooltip.css').'" type="text/css" media="all" />';
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('sjcycle.css').'" type="text/css" media="all" />';
	}
	return $flux;
}

function sjcycle_insert_head($flux){
	$conf_jcycle = lire_config('sjcycle');
	$flux .="\n".'<script src="'.find_in_path('javascript/jquery.cycle.all.js').'" type="text/javascript"></script>';
	if($conf_jcycle['tooltip']) {
		$flux .="\n".'<script src="'.find_in_path('javascript/jquery.tooltip.js').'" type="text/javascript" charset="utf-8"></script>';
	}
	
	return $flux;
}

?>