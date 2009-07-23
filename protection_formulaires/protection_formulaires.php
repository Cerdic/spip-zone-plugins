<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_PROTEGER_FORMULAIRES',(_DIR_PLUGINS.end($p)));


function protection_formulaires_proteger($data) {
	$ret = '<script src="'._DIR_PLUGIN_PROTEGER_FORMULAIRES.'/javascript/proteger_formulaires.js" type="text/javascript"></script>';


	return $data.$ret;

}

?>