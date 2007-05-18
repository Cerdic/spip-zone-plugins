<?php
if (!defined('_DIR_PLUGIN_SPIPICIOUS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIPICIOUS',(_DIR_PLUGINS.end($p)));
}

include_spip('base/spipicious');
?>