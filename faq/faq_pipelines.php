<?php

function faq_head(){
	$css ="";
	$css .= '<script src="'.find_in_path('faq.js').'" type="text/javascript"></script>';
	$css .= '<link rel="stylesheet" href="'.find_in_path('faq.css').'" type="text/css" media="all" />';
	return $css;
}

function faq_insert_head($flux) {
	if (intval($GLOBALS['spip_version_branche'])<3){
		$flux .= faq_head();
	}
	return $flux;
}

function faq_insert_head_css($flux) {
	if (intval($GLOBALS['spip_version_branche'])>=3){
		$flux .= faq_head();
	}
	return $flux;
}

?>