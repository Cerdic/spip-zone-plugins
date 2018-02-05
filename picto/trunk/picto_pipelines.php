<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function picto_insert_head_css($flux){
	$flux.="\n".'<link rel="stylesheet" type="text/css" media="all" href="'.find_in_path('fontAwesome/css/font-awesome.min.css').'" />';
		$flux.="\n".'<link rel="stylesheet" type="text/css" media="all" href="'.find_in_path('css/picto.css').'" />';
	return $flux;
}



?>
