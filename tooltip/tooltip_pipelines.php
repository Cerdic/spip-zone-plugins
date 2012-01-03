<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function tooltip_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/tooltip.css').'" media="all" />'."\n";
	return $flux;
}

function tooltip_insert_head($flux) {
	$flux .=
		'<script type="text/javascript" src="'.find_in_path('lib/bgiframe.js').'" ></script>'."\n"
		.'<script type="text/javascript" src="'.find_in_path('lib/delegate.js').'" ></script>'."\n"
		.'<script type="text/javascript" src="'.find_in_path('lib/dimensions.js').'" ></script>'."\n"
		.'<script type="text/javascript" src="'.find_in_path('demo/chili-1.7.pack.js').'" ></script>'."\n"
		.'<script type="text/javascript" src="'.find_in_path('js/tooltip.js').'" ></script>';

	return $flux;
}

?>