<?php

function liens_sortants_ouvrants_insert_head($flux) {
	$flux .= '<script type="text/javascript">var liens_sortants_site = \''.$GLOBALS['meta']['adresse_site'].'\';</script>';
	$flux .= '<script  src="'.find_in_path('liens_sortants_ouvrants.js').'" type="text/javascript"></script>';
	$flux .= '<link rel="stylesheet" href="'.find_in_path('liens_sortants_ouvrants.css').'" type="text/css" media="all" />';
	return $flux;
}

?>