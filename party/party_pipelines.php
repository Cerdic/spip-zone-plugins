<?php

function party_insert_head($flux) {
	$flux .= "\n<link rel=\"stylesheet\" href=\"" .
    direction_css(find_in_path('party.css')) .
    "\" type=\"text/css\" media=\"all\" />\n";
//	$flux .= "\n<script type=\"text/javascript\" src=\"".find_in_path('party.js')."\"> </script> \n";
	return $flux;
}

function party_boussole($flux) {
	$flux .= recuperer_fond('party');
	return $flux;
}

?>