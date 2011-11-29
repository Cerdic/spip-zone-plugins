<?php
/**
 * Plugin NivoSlider pour Spip 3.0
 * Licence GPL (c)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function nivoslider_insert_head_css($flux){
	include_spip('inc/filtres');
	$css = produire_fond_statique("css/nivoslider.css");
	$flux .= '<link rel="stylesheet" href="'.$css.'" type="text/css" media="all" />';
	return $flux;
}

function nivoslider_insert_head($flux){
	$flux .= '<script src="'.find_in_path('js/jquery.nivo.slider.pack.js').'" type="text/javascript"></script>';
	return $flux;
}

?>
