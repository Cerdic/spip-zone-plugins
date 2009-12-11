<?php

function galerie_carree_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('galerie_carree.css').'" type="text/css" media="all" />';
	return $flux;
}

?>