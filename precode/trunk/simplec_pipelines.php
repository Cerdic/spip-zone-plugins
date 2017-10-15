<?php

function simplec_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/simplec.css').'" type="text/css" media="all" />';
	return $flux;
}

?>