<?php

/*
 * P.I.M Agenda
 * Gestion d'un agenda collaboratif
 *
 * Auteur :
 * Cedric Morin, Notre-ville.net
 * (c) 2005,2007 - Distribue sous licence GNU/GPL
 *
 */

if (!defined('_DIR_PLUGIN_PIMAGENDA')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PIMAGENDA',(_DIR_PLUGINS.end($p)));
}
include_spip('base/pim_agenda');
?>