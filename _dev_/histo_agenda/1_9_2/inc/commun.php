<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));

define('_DIR_PLUGIN_HA',(_DIR_PLUGINS.end($p)));
define('_URL_PLUGIN_HA','../plugins/histo_agenda_1_9_2');

// nombre de jours au bout duquel les pages stockees
// sont effacees du disque
define("HA_NB_JOURS", 15);
define('_DIR_PLUGIN_HA_ARCH', _DIR_PLUGIN_HA."/archives");


?>