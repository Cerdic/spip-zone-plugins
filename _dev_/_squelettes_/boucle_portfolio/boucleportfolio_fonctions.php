<?php

function boucleportfolio_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('boucle_portfolio.css').'" type="text/css" media="projection, screen" />';
	return $flux;
}
?>
