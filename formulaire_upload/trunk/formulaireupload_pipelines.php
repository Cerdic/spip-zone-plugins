<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaireupload_insert_head($flux){
	$flux .=  '<script src="'.find_in_path('javascript/jquery.multifile.js').'" type="text/javascript"></script>';     // fourni par mediatheque
	return $flux;
}


function formulaireupload_insert_head_css($flux){
	$flux .=  '<link rel="stylesheet" href="'.find_in_path('css/formulaireupload.css').'" type="text/css" media="all" />';	
	return $flux;
}

?>