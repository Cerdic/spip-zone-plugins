<?php

function sisyphus_insert_head($flux){
	$flux .='<script src="'.find_in_path('javascript/sisyphus.js').'" type="text/javascript"></script>';
	$flux .='<script>$(function(){$("form").sisyphus();})</script>';
	return $flux;
}

function sisyphus_jquery_plugins($plugins){
	$plugins[] = 'javascript/sisyphus.js';
	return $plugins;
}

?>
