<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Pour spip2
function accordeon_jqueryui_forcer($array){
	$array[] ='jquery.ui.accordion';
	return $array;	
}

// Pour Spip3
function accordeon_jqueryui_plugins($array){
	$array[] ='jquery.ui.accordion';
	return $array;	
}

function accordeon_insert_head($flux){
	$flux .="\n<script type='text/javascript' src='".timestamp(produire_fond_statique('accordeon.js'))."'></script>";
	return $flux;
}
?>