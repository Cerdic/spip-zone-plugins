<?php

//
// ajout feuille de style
//
function partager_insert_head_css($flux){
	static $done = false;
	if ($done) return $flux;
	$done = true;
	
	$css = find_in_path('partager.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";
	return $flux;
}

function partager_insert_head($flux){
	$flux = partager_insert_head_css($flux); // au cas ou il n'est pas implemente
	return $flux;
}

?>