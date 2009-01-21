<?php



function css_petits_ecrans_insert_head($flux) {

	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_CSS_PETITS_ECRANS',(_DIR_PLUGINS.end($p)));

	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_CSS_PETITS_ECRANS.'css_petits_ecrans.css" type="text/css" media="all" />';
	
	return $flux;
}

?>