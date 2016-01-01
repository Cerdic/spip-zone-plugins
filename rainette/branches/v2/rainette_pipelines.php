<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Insertion des css de Rainette
function rainette_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="' . find_in_path('rainette.css') . '" type="text/css" media="all" />';
	}
	return $flux;
}
function rainette_insert_head($flux){
	$flux .= rainette_insert_head_css($flux); // au cas ou il n'est pas implemente
	return $flux;
}
?>
