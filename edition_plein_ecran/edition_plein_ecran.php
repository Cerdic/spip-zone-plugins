<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EDITION_PLEIN_ECRAN',(_DIR_PLUGINS.end($p)));


function edition_plein_ecran_charger_script ($data) {


	$ret = '<script src="'._DIR_PLUGIN_EDITION_PLEIN_ECRAN.'javascript/edition_plein_ecran.js" type="text/javascript"></script>';


	return $data.$ret;

}

?>