<?php


	/**
	 * SPIP-Trad
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined('_DIR_PLUGIN_TRAD')) {
		$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_TRAD', (_DIR_PLUGINS.end($p)));
		define('_NOM_PLUGIN_TRAD', (end($p)));
	}


?>