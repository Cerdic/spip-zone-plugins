<?php

/**********
 * PUBLIC *
 **********/

function fluxslider_insert_head_css($flux_ = '', $prive = false){
	static $done = false;
	if($done) return $flux_;
	$done = true;
	$flux  = "<link rel='stylesheet' type='text/css' href='".find_in_path('fluxslider.css')."' />";
	return $flux_ . $flux;
}

function fluxslider_insert_head($flux_){
	$flux .= "<script type='text/javascript' src='".find_in_path('js/flux.js')."'></script>";
	return $flux_ . fluxslider_insert_head_css() . $flux;
}

/*********
 * PRIVE *
 *********/

function fluxslider_header_prive($flux_){
	$flux .= "<script type='text/javascript' src='".find_in_path('js/flux.js')."'></script>
";
	return $flux_ . fluxslider_insert_head_css() . $flux;
}

?>
