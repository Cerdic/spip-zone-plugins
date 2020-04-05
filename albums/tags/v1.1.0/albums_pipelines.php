<?php

function albums_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/albums.css').'" type="text/css" media="all" />';
	return $flux;
}

?>