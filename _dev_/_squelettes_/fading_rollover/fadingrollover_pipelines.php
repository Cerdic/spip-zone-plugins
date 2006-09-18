<?php

function fadingrollover_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('fadingrollover.js')."'></script>\n";
	return $flux;
}

?>
