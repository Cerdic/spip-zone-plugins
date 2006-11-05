<?php
function arbodyn_header_prive($flux){

	$flux.='<script type="text/javascript"><!--
var img_deplierhaut = "'._DIR_PLUGIN_ARBODYN.'noeud_plus.gif";
var img_deplierbas = "'._DIR_PLUGIN_ARBODYN.'noeud_moins.gif";
//--></script>';
	$flux .= '<script src="'.find_in_path('dragdrop_interface.js').'" type="text/javascript"></script>';
	$flux .= '<script src="'.find_in_path('tree_edite.js').'" type="text/javascript"></script>';
	$flux .= '<script src="'.find_in_path('pause.js').'" type="text/javascript"></script>';

	return $flux;
}
	
function arbodyn_affiche_droite($flux){
	$flux['data'].=arbodyn_edite();
	return $flux;
}
function arbodyn_edite(){
	$out = "";
	
	return $out;
}

?>