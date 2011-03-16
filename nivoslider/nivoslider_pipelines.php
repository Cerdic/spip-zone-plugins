<?php

function nivoslider_insert_head($flux){ 
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.url_absolue(generer_url_public('css_nivoslider')).'" type="text/css" media="all" />';
		$flux .= '<script src="'.url_absolue(find_in_path('js/jquery.nivo.slider.pack.js')).'" type="text/javascript"></script>';
	}
	return $flux;
}

?>
