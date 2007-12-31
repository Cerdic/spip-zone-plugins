<?php

function gazette_insert_head($flux) {
	$flux .= "\n<link rel=\"stylesheet\" href=\"" .
    direction_css(find_in_path('gazette.css')) .
    "\" type=\"text/css\" media=\"all\" />\n";
	return $flux;
}

function gazette_boussole($flux) {
	$flux .= recuperer_fond('gazette');
	return $flux;
}

?>