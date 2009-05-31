<?php
	function recherche_insert_head($flux) {
		return $flux ."<link rel=\"stylesheet\" type=\"text/css\" href=\"".direction_css(find_in_path("recherche.css"))."\" media=\"projection, screen, tv\" />\n";
	}
?>