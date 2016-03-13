<?php

function fil_ariane_insert_head($flux){
	$flux .= '<!-- insertion de la css fil_ariane --><link rel="stylesheet" type="text/css" href="'.find_in_path('fil_ariane.css').'" media="all" />';
	return $flux;
}

?>