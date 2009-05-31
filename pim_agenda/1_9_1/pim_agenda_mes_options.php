<?php
if (!defined('_DIR_PLUGIN_PIMAGENDA')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PIMAGENDA',(_DIR_PLUGINS.end($p)));
}
include_spip('base/pim_agenda');
?>