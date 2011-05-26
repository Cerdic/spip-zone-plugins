<?php

/**********
 * PUBLIC *
 **********/

function nivoslider_insert_head_css($flux_ = '', $prive = false){
	static $done = false;
	if($done) return $flux_;
	$done = true;
	$flux='<link rel="stylesheet" href="'.url_absolue(generer_url_public('css_nivoslider')).'" type="text/css" media="all" />';
	return $flux_ . $flux;
}

function nivoslider_insert_head($flux_){
	$flux='<script src="'.url_absolue(find_in_path('js/jquery.nivo.slider.pack.js')).'" type="text/javascript"></script>';
	return $flux_ 
		. nivoslider_insert_head_css() // en cas d'absence de balise #INSERT_HEAD_CSS
		. $flux;
}

/*********
 * PRIVE *
 *********/

function nivoslider_header_prive($flux_){
	$flux = nivoslider_insert_head_prive($flux);
	return $flux_ . $flux;
}

function nivoslider_insert_head_prive($flux_){
	return $flux_ 
		. nivoslider_insert_head()
		. $flux;
}


?>
