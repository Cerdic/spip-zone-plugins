<?php

function tourinfrance_header_prive($flux){
	$flux = tourinfrance_insert_head_css($flux);
	return $flux;
}

function tourinfrance_insert_head_css($flux) {

    $flux .= '<link rel="stylesheet" href="' . find_in_path('css/tourinfrance.css') . ' type="text/css" media="all" />';
    return $flux;
	
}

?>