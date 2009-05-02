<?php


	if (!defined('_DIR_PLUGIN_FACTEUR')) {
		$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_FACTEUR', (_DIR_PLUGINS.end($p)));
		define('_NOM_PLUGIN_FACTEUR', (end($p)));
	}


?>