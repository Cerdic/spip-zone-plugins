<?php

function cloudzoom_insert_head($flux){
	$js = find_in_path('js/cloud-zoom.1.0.2.min.js');
	$flux	.= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('cloudzoom.css').'" media="all" />'."\n"

		.  "<script type='text/javascript' src='$js'></script>\n";
	return $flux;

}








