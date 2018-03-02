<?php

function stats_data_header_prive($flux){
	$css = find_in_path('css/stats_data.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n" ;
	return $flux;
}
