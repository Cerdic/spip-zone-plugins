<?php

//$spip_pipeline[insert_head] = str_replace('|f_jQuery', '' , $spip_pipeline[insert_head]); 


if (!defined('_DIR_PLUGIN_PANORAMAS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PANORAMAS',(_DIR_PLUGINS.end($p)));
}
include_spip('base/panoramas_visites_virtuelles');



?>