<?php
if (!defined('_DIR_PLUGIN_YMEDIAPLAYER')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_YMEDIAPLAYER',(_DIR_PLUGINS.end($p)."/"));
}
 
//
// ajout JS
//

function ymediaplayer_insert_head($flux){
	
	$flux .= '<script type="text/javascript" src="http://mediaplayer.yahoo.com/js"></script>';
	return $flux;
}
?>