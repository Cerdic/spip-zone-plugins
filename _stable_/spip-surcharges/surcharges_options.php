<?php


	/**
	 * SPIP-Surcharges
	 *
	 * Copyright (c) 2006-2009 Artégo http://www.artego.fr
	 **/


	if (!defined('_DIR_PLUGIN_SURCHARGES')) {
		$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_SURCHARGES', (_DIR_PLUGINS.end($p)));
		define('_NOM_PLUGIN_SURCHARGES', (end($p)));
	}


?>