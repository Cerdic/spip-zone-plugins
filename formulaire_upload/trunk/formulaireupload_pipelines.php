<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaireupload_insert_head($flux){
  $flux .=  '<script src="'.find_in_path('javascript/jquery.MultiFile.js').'" type="text/javascript"></script>';     // fourni par mediatheque
	return $flux;
}


function formulaireupload_insert_head_css($flux){
	$flux .=  '<script src="'.find_in_path('css/formulaireupload.css').'" type="text/javascript"></script>';	
	return $flux;
}

?>
