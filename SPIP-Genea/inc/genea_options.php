<?php
/* *********************************************************************
   *
   * Copyright (c) 2007-2008
   * Xavier Burot
   * fichier : genea_options.php
   *
   * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
   *
   *********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// -- Definition du chemin du plugin SPIP-Genea -------------------------
if (!defined('_DIR_PLUGIN_SPIPGENEA')) { // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'\..')));
	define('_DIR_PLUGIN_SPIPGENEA',(_DIR_PLUGINS.end($p).'/'));
}

include_spip('base/genea_base');
include_spip('public/genea_boucles');
include_spip('public/genea_criteres');

// -- Definition du chemin du logo de base du plugin --------------------
if (!defined('_LOGO_SPIPGENEA')) @define("_LOGO_SPIPGENEA", _DIR_PLUGIN_SPIPGENEA."images/arbre-24.png");

?>