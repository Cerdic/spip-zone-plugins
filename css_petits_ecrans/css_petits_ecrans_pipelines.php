<?php


function css_petits_ecrans_insert_head($flux) {

	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_CSS_PETITS_ECRANS.'css_petits_ecrans.css" type="text/css" media="all" />';
	
	return $flux;
}

?>