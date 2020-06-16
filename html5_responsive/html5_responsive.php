<?php

if (!defined("_HTML5_RESPONSIVE_ACCESSIBLE")) define("_HTML5_RESPONSIVE_ACCESSIBLE", true);



function html5_responsive_insert_head($flux) {

	if (_HTML5_RESPONSIVE_ACCESSIBLE) $viewport ="width=device-width,viewport-fit=cover,initial-scale=1.0"; 
	else $viewport = "user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover";

	$flux = "<meta charset='".lire_meta("charset")."'>
	<meta name='viewport' content='$viewport'>
	<meta name='format-detection' content='telephone=no'>
	<meta http-equiv='x-ua-compatible' content='ie=edge'>
	<meta name='apple-mobile-web-app-capable' content='yes'>
	<meta name='apple-mobile-web-app-status-bar-style' content='black'>"
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