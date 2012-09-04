<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function imgcompare_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('qbeforeafter.css').'" type="text/css" media="projection, screen, tv" />';
	return $flux;
}

?>