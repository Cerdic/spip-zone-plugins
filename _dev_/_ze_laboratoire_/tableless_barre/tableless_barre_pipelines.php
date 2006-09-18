<?php

function tableless_barre_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('tableless_barre.css').'" type="text/css" media="projection, screen" />';
	return $flux;
}

function tableless_barre_insert_head($flux){
	return tableless_barre_header_prive($flux);
}

?>
