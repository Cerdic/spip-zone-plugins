<?php

function docboiteplus_insert_head($flux){

$flux ='
<link rel="stylesheet" href="'.url_absolue(find_in_path('docboiteplus.css')).'" type="text/css" media="projection, screen" />
';

	return $flux;
}

?>
