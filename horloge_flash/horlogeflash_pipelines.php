<?php

// Insertion des css de l'horloge
function horlogeflash_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="' . find_in_path('jClocksGMT/css/jClocksGMT.css') . '" type="text/css" media="all" />';
	}
	return $flux;
}
function horlogeflash_insert_head($flux){
	$flux .= horlogeflash_insert_head_css($flux); // au cas ou il n'est pas implemente
	$flux .= '<script src="' . find_in_path('jClocksGMT/js/jquery.rotate.js') . '" type="text/javascript"></script>';
	$flux .= '<script src="' . find_in_path('jClocksGMT/js/jClocksGMT.js') . '" type="text/javascript"></script>';
	return $flux;
}
