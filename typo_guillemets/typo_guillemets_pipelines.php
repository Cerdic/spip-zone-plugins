<?php

function typo_guillemets_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('typo_guillemets.css').'" type="text/css" media="all" />';
	return $flux;
}

?>