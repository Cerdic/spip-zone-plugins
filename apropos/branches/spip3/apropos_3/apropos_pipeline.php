<?php
//
// ajout feuille de style
//
function apropos_insert_head($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('apropos.css').'" media="all" />'."\n";
	return $flux;
}
?>