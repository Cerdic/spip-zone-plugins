<?php

function realistik_header_prive($flux){

	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ICONES_ADMIN',(_DIR_PLUGINS.end($p)));

	global $exec;
	
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_ICONES_ADMIN.'/../img_pack/style.css" />'."\n";
	
	return $flux;
}

?>