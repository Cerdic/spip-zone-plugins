<?php

if (!defined('_DIR_PLUGIN_SMOOTH')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SMOOTH',(_DIR_PLUGINS.end($p)."/"));
}

function smooth_insert_head($flux) {
	$flux .= '<script src="'._DIR_PLUGIN_SMOOTH.'scripts/prototype.lite.js" type="text/javascript"></script>';
	$flux .= '<script src="'._DIR_PLUGIN_SMOOTH.'scripts/moo.fx.js" type="text/javascript"></script>';
	$flux .= '<script src="'._DIR_PLUGIN_SMOOTH.'scripts/moo.fx.pack.js" type="text/javascript"></script>';
	$flux .= '<script src="'._DIR_PLUGIN_SMOOTH.'scripts/moo.fx.utils.js" type="text/javascript"></script>';
	$flux .= '<script src="'._DIR_PLUGIN_SMOOTH.'scripts/timed.slideshow.js" type="text/javascript"></script>';
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_SMOOTH.'css/jd.slideshow.css" type="text/css" media="screen" />';
	return $flux;
}

?>