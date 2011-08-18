<?php

function skiplink_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/skiplink.css').'" media="all" />'."\n";
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/focus.css').'" media="all" />'."\n";
	return $flux;
}

?>