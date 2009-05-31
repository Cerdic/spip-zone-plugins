<?php

function slideshow_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('interface.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="spip.php?page=slideshow.css" type="text/css" media="projection, screen" />';
	return $flux;
}
?>