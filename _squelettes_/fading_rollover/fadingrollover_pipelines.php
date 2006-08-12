<?php

function fadingrollover_insert_head($flux){
	$flux .= '<script src="'.find_in_path('jquery-1.0a.js').'" type="text/javascript"></script>';
	$flux .= "<script type='text/javascript' src='".find_in_path('fadingrollover.js')."'></script>\n";
	return $flux;
}

?>
