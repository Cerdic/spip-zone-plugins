<?php


	/**
	 * Livre d'or
	 *
	 * Copyright (c) 2006
	 * Bernard Blazin  http://www.libertyweb.info
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_LIVRE', (_DIR_PLUGINS.end($p)));
	define('_NOM_PLUGIN_LIVRE', (end($p)));

	include_spip('inc/livre_fonctions');

	
?>