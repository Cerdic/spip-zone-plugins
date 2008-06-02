<?php
if (!defined('_DIR_PLUGIN_SPIP_THELIA')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIP_THELIA',(_DIR_PLUGINS.end($p)));
}
include_spip('base/spip_thelia_produits_associes');



?>