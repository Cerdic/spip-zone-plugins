<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ezcss_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/ez-plug.css').'" media="all" />';

	return $flux;
}
