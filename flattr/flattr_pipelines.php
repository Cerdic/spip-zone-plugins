<?php

function flattr_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/flattr.css').'" media="all" />'."\n";
	return $flux;
}

?>