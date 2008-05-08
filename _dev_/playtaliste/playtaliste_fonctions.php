<?php
include_spip('inc/cookie');

function balise_PLAYTALISTE_LISTE($p) {
	$p->code = 'unserialize($_COOKIE["playtaliste"])';
	$p->type = 'php';  
	return $p;
}
function playtaliste_insert_head($flux){
	$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_PLAYTALISTE."playtaliste.css' type='text/css' media='all' />\n";
	return $flux;
}
?>
