<?php

// Charge la CSS si pas encore fait, par compatibilite avec SPIP 2.0 (pas encore
// de #INSERT_HEAD_CSS) puis insere un hack javascript pour IE
function html5_insert_head($flux) {
	$javascript_ie = find_in_path('javascript/html5-ie.js');
	$flux .= html5_insert_head_css('')
		. "<!--[if IE]>\n"
		. "<script src='$javascript_ie' type='text/javascript'></script>\n"
		. "<![endif]-->\n";
	return $flux;
}

function html5_insert_head_css($flux='') {
	static $html5_headcss_done = false;
	if (!$html5_headcss_done) {
		$reset = find_in_path('reset_html5.css');
		$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$reset' />\n";
    $html5_headcss_done = true;
	}
	return $flux;
}
