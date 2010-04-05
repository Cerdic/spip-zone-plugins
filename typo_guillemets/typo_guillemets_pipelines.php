<?php

function typo_guillemets_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('typo_guillemets.css').'" media="all" />'."\n";
	return $flux;
}

?>