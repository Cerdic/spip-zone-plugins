<?php

function galleria_insert_head($flux){
	$flux .= '<!-- insert_head -->';
	$flux .= insert_js();
	return $flux;
}

function galleria_header_prive($flux){
	include_spip("inc/filtres");
	$flux .= '<!-- header_prive -->';
	$flux .= insert_js();
	return $flux;
}

function insert_js(){
	return '<script type="text/javascript" src="'.url_absolue(find_in_path('galleria/galleria-1.2.5.min.js')).'"></script>';
}

?>
