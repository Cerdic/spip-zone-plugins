<?php

if (!defined('_DIR_PLUGIN_GETID3')){
	        $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	        define('_DIR_PLUGIN_GETID3',(_DIR_PLUGINS.end($p)).'/');
}

?>