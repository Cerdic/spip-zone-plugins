<?php

function portfolio_insert_head($flux){
	$flux .= 	"<link rel='stylesheet' href='".find_in_path('portfolio.css')."' type='text/css' media='all' />\n";
	return $flux;
}

?>