<?php
function arbodyn_header_prive($flux){

	$flux.='<script type="text/javascript"><!--
var img_deplierhaut = "'._DIR_IMG_PACK.'deplierhaut.gif";
var img_deplierbas = "'._DIR_IMG_PACK.'deplierbas.gif";
//--></script>';
	$flux .= '<script src="'.find_in_path('dragdrop_interface.js').'" type="text/javascript"></script>';
	$flux .= '<script src="'.find_in_path('tree_edite.js').'" type="text/javascript"></script>';
	$flux .= '<script src="'.find_in_path('pause.js').'" type="text/javascript"></script>';

	return $flux;
}
	
function arbodyn_affiche_droite(){
	return $flux.arbodyn_edite();
}
function arbodyn_edite(){
	$out = "";
	
	return $out;
}

?>