<?php

function petronille_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('petronille.css').'" type="text/css" media="all" />';
	return $flux;
}

?>