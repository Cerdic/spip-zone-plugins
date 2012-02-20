<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function bilancontrib_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/visualize.css').'" type="text/css" media="projection, screen, tv" />';
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/visualize-light.css').'" type="text/css" media="projection, screen, tv" />';
	return $flux;
}
?>