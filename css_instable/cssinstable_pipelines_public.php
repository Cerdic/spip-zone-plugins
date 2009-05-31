<?php
if (!defined('_CSSINSTABLE')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_CSSINSTABLE',(_DIR_PLUGINS.end($p)."/"));
}

function cssinstable_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_CSSINSTABLE.'js/css_instable.js"></script>';
	return $flux;
}
?>