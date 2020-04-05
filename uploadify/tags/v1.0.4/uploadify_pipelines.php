<?php
function uploadify_insert_head_css($flux){
	$f = find_in_path("theme/uploadify/uploadify.css");
	$flux .= '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="all" />';
	return $flux;
}
