<?php

function html5_responsive_insert_head($flux) {
	$flux = "<meta name='viewport' content='user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0'>
	<meta name='format-detection' content='telephone=no'>
	<meta name='apple-mobile-web-app-capable' content='yes'>
	<meta name='apple-mobile-web-app-status-bar-style' content='black'>
	<meta charset='".lire_meta("charset")."'>"
				.$flux;
	$flux .= "
<script type='text/javascript' src='".find_in_path("javascript/liens-standalone.js")."'></script>
<!--[if lt IE 9]>
<script type='text/javascript' src='".find_in_path("javascript/html5shiv.js")."'></script>
<script type='text/javascript' src='".find_in_path("javascript/css3-mediaqueries.js")."'></script>
<![endif]-->";
	
	return $flux;
}

function html5_responsive_insert_head_css($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("css/html5_responsive.css")."'>\n";

	return $flux;
}


?>