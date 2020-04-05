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

function nivoslider_insert_head($flux_ = ''){
	$flux='<script src="'.url_absolue(find_in_path('js/jquery.nivo.slider.pack.js')).'" type="text/javascript"></script>';
	return nivoslider_insert_head_css( $flux_) . $flux;
}

/*********
 * PRIVE *
 *********/

function nivoslider_header_prive($flux_ = ''){
	return nivoslider_insert_head_prive($flux_);
}

function nivoslider_insert_head_prive($flux_ = ''){
	return nivoslider_insert_head($flux_);
}

?>
