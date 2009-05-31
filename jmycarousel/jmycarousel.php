<?php
function jmycarousel_insert_head($flux){
		$flux .= '<link rel="stylesheet" href="'.find_in_path('css/jMyCarousel.css').'" type="text/css" media="projection, screen, tv" />'."\n";
		return $flux;
}

?>