<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function animatecss_insert_head_css($flux){
	$flux.="\n".'<link rel="stylesheet" type="text/css" media="all" href="'.find_in_path('animatecss/css/animate.css').'" />';
	return $flux;
}



?>
