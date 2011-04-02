<?php

function accordeon_jqueryui_forcer($array){
	$array[] ='jquery.ui.accordion';
	return $array;	
}

function accordeon_insert_head($flux){
	$flux .="<script type='text/javascript' src='".generer_url_public('accordeon.js')."'></script>";
	return $flux;
}
?>