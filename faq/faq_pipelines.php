<?php

function faq_insert_head($flux) {
	$flux .= '<script src="'.find_in_path('faq.js').'" type="text/javascript"></script>';
	$flux .= '<link rel="stylesheet" href="'.find_in_path('faq.css').'" type="text/css" media="all" />';
	return $flux;
}

?>