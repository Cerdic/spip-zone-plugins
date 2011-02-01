<?php

function links_insert_head($flux) {
	$flux .= '<script type="text/javascript">var liens_sortants_site = \''.$GLOBALS['meta']['adresse_site'].'\';</script>';
	$flux .= '<script  src="'.find_in_path('links.js').'" type="text/javascript"></script>';
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/links.css').'" type="text/css" media="all" />';
	return $flux;
}

?>