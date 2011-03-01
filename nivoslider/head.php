<?php

if (!defined('_DIR_PLUGIN_NIVOSLIDER')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_NIVOSLIDER',(_DIR_PLUGINS.end($p)));
}

function nivoslider_insert_head($flux){
	$flux .= "<link rel='stylesheet' type='text/css' href='spip.php?page="._DIR_PLUGIN_NIVOSLIDER."css/nivo-slider.css' />\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path(_DIR_PLUGIN_NIVOSLIDER.'js/jquery.nivo.slider.pack.js').'"></script>\n';
	return $flux;
}
?>
