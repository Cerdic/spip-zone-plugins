<?php

function nuage_insert_head($flux) {
	$css = "\n<link rel=\"stylesheet\" href=\"" .
    direction_css(find_in_path('nuage.css')) .
    "\" type=\"text/css\" media=\"all\" />\n";
	return $css.$flux;
}

?>