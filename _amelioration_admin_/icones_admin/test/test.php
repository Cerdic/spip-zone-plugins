<?php
	
function test_mes_options($flux) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_TEST',(_DIR_PLUGINS.end($p)));
	$directory = _DIR_PLUGIN_TEST;
	$flux = "define('_DIR_IMG_PACK', ('$directory/img_pack/'));";
	return $flux;
}

?>