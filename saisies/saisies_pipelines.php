<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function saisies_header_prive($flux){
	$js = find_in_path('javascript/saisies.js');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	$flux .= saisies_insert_head_css('');
	return $flux;
}

function saisies_insert_head($flux){
	$js = find_in_path('javascript/saisies.js');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	$flux .= saisies_insert_head_css(''); // compat pour les vieux spip
	return $flux;
}

function saisies_insert_head_css($flux){
	static $done = false;
	if ($done) return $flux;
	$done = true;
	
	$css = generer_url_public('saisies.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";
	return $flux;
}

?>
