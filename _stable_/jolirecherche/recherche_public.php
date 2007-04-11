<?php
	function recherche_insert_head($flux) {
		return $flux."<link rel=\"stylesheet\" type=\"text/css\" href=\"".direction_css(find_in_path("recherche.css"))."\" />\n<meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\" />\n<script src=\"".find_in_path("recherche.js")."\" type=\"text/javascript\"></script>";
	}
?>