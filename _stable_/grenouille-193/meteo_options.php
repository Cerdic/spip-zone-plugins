<?php

	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	 
	if (!defined('_DIR_PLUGIN_METEO')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_METEO',(_DIR_PLUGINS.end($p)));
	define('_NOM_PLUGIN_METEO', (end($p)));
	 }

	include_spip('base/meteo');
	include_spip('inc/vieilles_defs');

?>
