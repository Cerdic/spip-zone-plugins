<?php

function faq_css(){
	$css ="";
	$css .= '<link rel="stylesheet" href="'.find_in_path('css/faq.css').'" type="text/css" media="all" />';
	return $css;
}

function faq_insert_head($flux) {
	if (intval($GLOBALS['spip_version_branche'])<3){
		$flux .= faq_css();
	}
	$flux .= '<script src="'.find_in_path('js/faq.js').'" type="text/javascript"></script>';
	return $flux;
}

function faq_insert_head_css($flux) {
	if (intval($GLOBALS['spip_version_branche'])>=3){
		$flux .= faq_css();
	}
	return $flux;
}

?>