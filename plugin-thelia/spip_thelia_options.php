<?php

/* Repertoire de l'admin THELIA */
define('_THELIA_ADMIN', 'admin'); //obsolete cette variable est désormais définie dans le formulaire CFG du plugin

if (!defined('_DIR_PLUGIN_SPIP_THELIA')){
	$p = explode(basename(_DIR_PLUGINS) . "/", str_replace('\\', '/', realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIP_THELIA', (_DIR_PLUGINS . end($p)));
}

/* Chemin relatif du repertoire contenant Thelia */
/* Par défaut on Thelia est à la racine du site */
if (!defined('_RACINE_THELIA')){
	define('_RACINE_THELIA', './');
}

/* Un pipeline post authenfication thelia */
$GLOBALS['spip_pipeline']['thelia_authentifie'] .= '';

$thelia_path = ini_get("include_path") . ":" . _RACINE_THELIA;
@ini_set('include_path', $thelia_path);

include_spip('base/spip_thelia_produits_associes');
$GLOBALS['liste_des_authentifications']['thelia'] = 'thelia';
if (!function_exists('lire_config')){
	include_spip('inc/config');
}
$GLOBALS['thelia_statut_nouvel_auteur'] = lire_config("spip_thelia/statutclients_spip_thelia", "6forum");

