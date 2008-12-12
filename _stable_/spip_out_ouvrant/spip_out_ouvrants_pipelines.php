<?php



function spip_out_ouvrants_insert_head($flux) {

	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIP_OUT_OUVRANTS',(_DIR_PLUGINS.end($p)));


	$flux .= '<script  src="'._DIR_PLUGIN_SPIP_OUT_OUVRANTS.'spip_out_ouvrants.js" type="text/javascript"></script>';
	
	return $flux;
}

?>