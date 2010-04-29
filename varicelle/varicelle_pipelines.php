<?php

function varicelle_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('varicelle.css').'" media="all" />'."\n";
	return $flux;
}

?>