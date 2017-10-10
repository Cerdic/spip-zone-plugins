<?php
/**
 * Plugin Galleria
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function galleria_insert_head($flux){
	if (!function_exists('url_absolue')) {
		include_spip("inc/filtres");
	}
	$flux .= '<script type="text/javascript" src="'.find_in_path('galleria/galleria.min.js').'"></script>';
	return $flux;
}

