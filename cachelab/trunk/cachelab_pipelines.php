<?php

function cachelab_insert_head_css($flux) {
//  $css = find_in_path('css/cachelab.css');
//  $flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	$flux .= "
<style>
.cachelab_blocs {  
	margin: 5px; 
	background-color: lightyellow; 
	font-family: Courier, \"Courier New\", monospace;
    font-size: 0.8em;
	color: black;
}
.cachelab_blocs h6 { margin: 2px; font-size: 0.8em;}
.cachelab_blocs small {margin: 2px; padding: 2px; font-size: 60%;}
.cachelab_assert {background-color: orange}
</style>\n";
	return $flux;
}
