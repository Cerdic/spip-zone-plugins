<?php
function ajaxcallback_insert_head($flux){
	if (!defined(_DIR_JAVASCRIPT)){
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_AJAXCALLBACK',(_DIR_PLUGINS.end($p)));
		$flux .= '<script src="'._DIR_PLUGIN_AJAXCALLBACK.'/layer.js" type="text/javascript"></script>';
	}
	else	$flux .= '<script src="'._DIR_JAVASCRIPT.'layer.js" type="text/javascript"></script>';

	return $flux.'<script src="'.find_in_path('ajaxCallback.js').'" type="text/javascript"></script>';
}

?>
