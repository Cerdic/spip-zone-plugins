<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function menuder_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= "\n".'<link rel="stylesheet" href="'.direction_css(find_in_path('css/menuder.css')).'" type="text/css" media="projection, screen" />';
	}
	return $flux;
}

function menuder_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('js/menuder.js')."'></script>\n";
	$flux .= menuder_insert_head_css(''); // compat pour les vieux spip
	return $flux;
}
?>
