<?php
/**
 * plugin FreeRadio
 * Radio Web
 *
 * Auteurs :
 * Franck Ruzzin
 * le 09/05/2011
 *
 **/

if (!defined('_DIR_PLUGIN_FREERADIO')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FREERADIO',(_DIR_PLUGINS.end($p))."/");
}

function freeradio_insert_head($flux){	
	$flux .= "<script language='javascript' type='text/JavaScript'>var freeradioRoot=\"" . _DIR_PLUGIN_FREERADIO . "\"</script>\n";
	$flux .= "<script src='" . _DIR_PLUGIN_FREERADIO . "javascript/freeradio_spip-min.js' type='text/javascript'></script>\n";
	$flux .= "<link rel='stylesheet' href='" . _DIR_PLUGIN_FREERADIO . "css/freeradio.css' type='text/css' media='all' />\n";
	return $flux;
}
	
?>