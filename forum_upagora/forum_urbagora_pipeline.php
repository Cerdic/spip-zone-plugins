<?php

function forum_urbagora_insert_head($flux) {
	$css = "\n<link rel=\"stylesheet\" href=\"" . direction_css(find_in_path('forum_urbagora.css')) . "\" type=\"text/css\" media=\"all\" />\n";
	$js = "\n<script src=\"" . find_in_path('forum_urbagora.js') . "\" type=\"text/javascript\"></script>";
	return $flux.$css.$js;
}

?>
