<?php

/* Repertoire de l'admin THELIA */
define('_THELIA_ADMIN','admin'); //obsolete cette variable est désormais définie dans le formulaire CFG du plugin

if (!defined('_DIR_PLUGIN_SPIP_THELIA')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIP_THELIA',(_DIR_PLUGINS.end($p)));
}
include_spip('base/spip_thelia_produits_associes');
$GLOBALS['liste_des_authentifications']['thelia'] = 'thelia';
$GLOBALS['thelia_statut_nouvel_auteur'] = lire_config("spip_thelia/statutclients_spip_thelia", "6forum");
?>