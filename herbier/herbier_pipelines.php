<?php

function herbier_insert_head($flux) {
	$flux .= "\n<link rel=\"stylesheet\" href=\"" .
    direction_css(find_in_path('herbier.css')) .
    "\" type=\"text/css\" media=\"all\" />\n";
	return $flux;
}

function herbier_boussole($flux) {
	$flux .= recuperer_fond('herbier');
	return $flux;
}

?>