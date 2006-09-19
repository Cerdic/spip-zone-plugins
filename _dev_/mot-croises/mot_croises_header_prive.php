<?php 

function mot_croises_header_prive($flux){
	return $flux."<link rel=\"stylesheet\" type=\"text/css\" href=\"".direction_css(find_in_path("mots-croises-prive.css"))."\" />\n<script src=\"".find_in_path("mots-croises.js")."\" type=\"text/javascript\" />";
}
?>