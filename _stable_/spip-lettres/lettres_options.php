<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined('_DIR_PLUGIN_LETTRE_INFORMATION')) {
		$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_LETTRE_INFORMATION', (_DIR_PLUGINS.end($p)));
		define('_NOM_PLUGIN_LETTRE_INFORMATION', (end($p)));
		define('_DIR_LETTRES', _DIR_IMG.'lettres/');
	}


?>