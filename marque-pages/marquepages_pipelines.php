<?php

function marquepages_insert_head($flux) {
	$css = "\n<link rel=\"stylesheet\" href=\""
		 . direction_css(find_in_path('css/marquepages.css'))
		 . "\" type=\"text/css\" media=\"all\" />\n";
		 
	$js = "\n<script type=\"text/javascript\" src=\""
		 . generer_url_public('marquepages.js')
		 . "\"></script>\n";
	return $css.$js.$flux;
}

?>
