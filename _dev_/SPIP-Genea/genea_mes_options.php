<?php
/*	*********************************************************************
	*
	* Copyright (c) 2006
	* Xavier Burot
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

include_spip('base/genea_base');

// -- Definition du chemin du plugin GENEA -------------------------------
if (!defined('_DIR_PLUGIN_GENEA')) { // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_GENEA',(_DIR_PLUGINS.end($p)));
}
?>