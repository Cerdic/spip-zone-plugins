<?php

function fragahah_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('pagination-ahah.js')."'></script>\n";
	return $flux;
}


?>