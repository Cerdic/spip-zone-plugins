<?php
/*	*********************************************************************
	*
	* Copyright (c) 2007
	* Xavier Burot
	*
	* SPIP-ALBUM : Programme d'affichage de photos
	*
	* Fichier : album_options.php
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// -- Definition du chemin du plugin SPIP-Album -------------------------
if (!defined('_DIR_PLUGIN_ALBUM')) { // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ALBUM',(_DIR_PLUGINS.end($p).'/'));
}
?>