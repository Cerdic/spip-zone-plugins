<?php

function imagerotator_insert_head_css($flux){
	return $flux;
}

function imagerotator_insert_head($flux){
	$v = lire_config('imagerotator/swfobject','1.5');
	$flux .= ($v=='1.5') ? '<script src="'.url_absolue(find_in_path('swfobject.js')).'" type="text/javascript"></script>' : '<script src="'.url_absolue(find_in_path('lib/swfobject/swfobject.js')).'" type="text/javascript"></script>';
	return $flux;
}

?>
