<?php

function sjcycle_insert_head($flux){
	$conf_jcycle = lire_config('sjcycle');
	$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.cycle.all.min.js')).'" type="text/javascript"></script>';
	if($conf_jcycle["tooltip"]) {
		$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('javascript/jquery.tooltip.css')).'" type="text/css" media="all" />';
		$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.tooltip.js')).'" type="text/javascript" charset="utf-8"></script>';
	}
	/* options */
	$flux .= "\n".'<style type="text/css" media="all">
.'.$conf_jcycle["div_class"].'{
padding:0px;
margin:auto;
display:block;
clear:both;
}
</style>
'; 
	
	return $flux;
}

?>