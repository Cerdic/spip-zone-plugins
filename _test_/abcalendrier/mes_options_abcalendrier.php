<?php
/***************************************************************/
/*    Definizione del campo extra 'evento' per le brevi
/***************************************************************/
//error_reporting(E_ALL);

if (!defined('_DIR_PLUGIN_ABCALENDRIER')){
   $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
   define('_DIR_PLUGIN_ABCALENDRIER',(_DIR_PLUGINS.end($p)));
}
?>