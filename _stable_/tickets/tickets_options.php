<?php
if (!defined('_DIR_PLUGIN_TICKETS')) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_TICKETS',(_DIR_PLUGINS.end($p)).'/');
} 
if (!defined("_TICKETS_PREFIX")) define ("_TICKETS_PREFIX", "tickets");

?>
