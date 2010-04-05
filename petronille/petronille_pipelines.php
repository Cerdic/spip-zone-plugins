<?php

function petronille_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('reset.css').'" media="all" />'."\n";
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('typo.css').'" media="all" />'."\n";
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('forms.css').'" media="all" />'."\n";
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('spip.css').'" media="all" />';
	return $flux;
}

?>