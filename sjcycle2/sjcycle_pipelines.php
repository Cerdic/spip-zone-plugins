<?php

function sjcycle_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$conf_jcycle = lire_config('sjcycle');
		if($conf_jcycle["tooltip"]) {
			$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('javascript/jquery.tooltip.css')).'" type="text/css" media="all" />';
		}
		$flux .= '<link rel="stylesheet" href="'.url_absolue(generer_url_public('sjcycle.css')).'" type="text/css" media="all" />';
	}

	return $flux;
}

function sjcycle_insert_head($flux){
	$conf_jcycle = lire_config('sjcycle');
	$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.cycle.all.min.js')).'" type="text/javascript"></script>';
	if($conf_jcycle["tooltip"]) {
		$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.tooltip.js')).'" type="text/javascript" charset="utf-8"></script>';
	}

	$flux .= sjcycle_insert_head_css(''); // compat pour les vieux spip

	return $flux;
}

?>