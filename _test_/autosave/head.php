<?php

function autosave_insert_head($flux){

$flux .= '<script src="'.url_absolue(find_in_path('autosave.js')).'" type="text/javascript"/></script>' ;

	return $flux;
}

?>
