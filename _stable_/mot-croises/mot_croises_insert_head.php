<?php 

function mot_croises_insert_head($flux){
	return $flux."<link rel=\"stylesheet\" type=\"text/css\" href=\"".direction_css(find_in_path("mots-croises.css"))."\" />\n<script src=\"".find_in_path("mots-croises.js")."\" type=\"text/javascript\"></script>";}
?>
