<?php

function galcaro_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/galcaro.css').'" type="text/css" media="all" />';
	return $flux;
}

?>