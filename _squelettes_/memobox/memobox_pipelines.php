<?php

function memobox_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('memobox.css').'" type="text/css" media="projection, screen" />';
	$flux .= '<script src="'.find_in_path('jquery-1.0a.js').'" type="text/javascript"></script>';
	$flux .= "<script type='text/javascript' src='".find_in_path('memobox.js')."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path('idrag.js')."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path('idrop.js')."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path('iutil.js')."'></script>\n";
	return $flux;
}
?>
