<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_BUREAU',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

// Declaration des pipelines
$GLOBALS['spip_pipeline']['BUREAU_menus']='';
$GLOBALS['spip_pipeline']['BUREAU_infos']='';
$GLOBALS['spip_pipeline']['BUREAU_fenetres']='';
?>
