<?php
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_DIAPO',(_DIR_PLUGINS.end($p)));

function diapo_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('diapo.js').'"></script>';
	$flux .= '<link rel="stylesheet" href="'.find_in_path('diapo.css').'" type="text/css" media="all" />';
	return $flux;
}
function diapo_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('diapo.css').'" type="text/css" media="all" />';
	return $flux;
}
?>