<?php



function medias_responsive_insert_head_css($flux) {
	$flux = "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("css/medias_responsive.css")."'>\n".$flux;


	return $flux;
}

function medias_responsive_insert_head($flux) {
	$flux .= "<script async type='text/javascript' src='".find_in_path("javascript/medias_responsive.js")."'></script>\n";
	return $flux;
}

function medias_responsive_header_prive($flux) {
	$flux .= "<script async type='text/javascript' src='".find_in_path("javascript/medias_responsive.js")."'></script>\n";
	return $flux;
}

