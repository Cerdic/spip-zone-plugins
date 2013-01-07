<?php

function notessurgissantes_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$f = find_in_path('notessurgissantes.css');
		$flux .= '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="all" />';
	}
	return $flux;
}


function notessurgissantes_insert_head($flux){
	$flux = notessurgissantes_insert_head_css($flux); // au cas ou il n'est pas implemente

	$flux .='
<script src="'.(find_in_path('notessurgissantes.js')).'" type="text/javascript"></script>
';

	return $flux;
}
