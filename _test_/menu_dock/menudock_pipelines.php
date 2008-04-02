<?php

function menudock_insert_head($flux){
	$flux .= '
	<script type="text/javascript" src="'.find_in_path('js/interface.js').'"></script>
	<link rel="stylesheet" href="'.find_in_path('style.css').'" type="text/css" media="all" />';
	return $flux;
}

?>
