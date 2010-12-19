<?php
function liscroll_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('css/li-scroller.css')).'" type="text/css" media="projection, screen" />'."\n";
	}
	return $flux;
}


function liscroll_insert_head($flux){
	$flux = liscroll_insert_head_css($flux); // au cas ou il n'est pas implemente
	
	$flux .= "<script type='text/javascript' src='".find_in_path('js/jquery.li-scroller.1.0.js')."'></script>\n";
	$flux .= "<script type=\"text/javascript\">$(document).ready(function(){\n
		$('ul#ticker01').liScroll();\n
	});</script>\n";
	return $flux;
}
?>