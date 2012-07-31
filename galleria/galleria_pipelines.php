<?php
/**
 * Plugin Galleria
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function galleria_insert_head($flux){
	$flux .= insert_js();
	return $flux;
}

function galleria_header_prive($flux){
	include_spip("inc/filtres");
	$flux .= insert_js();
	return $flux;
}

function insert_js(){
	return '<script type="text/javascript" src="'.url_absolue(find_in_path('galleria/galleria.min.js')).'"></script>';
}

?>
