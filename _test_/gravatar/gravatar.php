<?php
	/**
	 *
	 * Gravatar : Globally Recognized AVATAR
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006
	 *
	 **/

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_GRAVATAR', (_DIR_PLUGINS.end($p)));

?>