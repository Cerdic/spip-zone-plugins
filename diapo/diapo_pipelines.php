<?php

function diapo_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.generer_url_public('diapo.js').'"></script>';
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('diapo.css').'" type="text/css" media="all" />';
	return $flux;
}
function diapo_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('diapo.css').'" type="text/css" media="all" />';
	return $flux;
}
?>