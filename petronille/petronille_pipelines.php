<?php

function petronille_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('reset.css').'" type="text/css" media="all" />'."\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('typo.css').'" type="text/css" media="all" />'."\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('spip.css').'" type="text/css" media="all" />';
	return $flux;
}

?>