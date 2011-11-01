<?php

function album_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/album.css').'" type="text/css" media="all" />';
	return $flux;
}

?>