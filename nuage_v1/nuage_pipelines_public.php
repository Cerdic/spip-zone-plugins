<?php
function nuage_insert_head_css($flux){ 
	static $done = false; 
	if (!$done) {
		$done = true;
		$flux .= "\n<link rel=\"stylesheet\" href=\"" .
		direction_css(find_in_path('nuage.css')) .
		"\" type=\"text/css\" media=\"all\" />\n";
		}
	return $flux;
}
function nuage_insert_head($flux) {
	$css = nuage_insert_head_css($flux); // au cas ou il n'est pas implemente
	return $css.$flux;
}
?>