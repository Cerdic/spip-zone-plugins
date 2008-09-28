<?php


	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Bernard Blazin  http://www.libertyweb.info & Yohann Prigent (potter64)
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