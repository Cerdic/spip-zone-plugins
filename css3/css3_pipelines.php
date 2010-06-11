<?php

function css3_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css3.css').'" media="all" />'."\n";
	return $flux;
}

?>