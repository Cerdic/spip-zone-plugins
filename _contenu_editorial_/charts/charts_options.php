<?php
/*
 * charts
 *
 * Auteur :
 * Cedric MORIN
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_CHARTS',(_DIR_PLUGINS.end($p)));

?>