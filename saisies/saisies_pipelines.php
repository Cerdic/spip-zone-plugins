<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function saisies_header_prive($flux){
	$js = find_in_path('javascript/saisies.js');
	$css = generer_url_public('saisies.css');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";
	return $flux;
}

function saisies_insert_head($flux){
	$js = find_in_path('javascript/saisies.js');
	$css = generer_url_public('saisies.css');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='projection, screen, tv' />\n";
	return $flux;
}

?>
