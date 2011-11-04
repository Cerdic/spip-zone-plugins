<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// URL supplémentaire où trouver de l'aide en ligne
$GLOBALS['help_server'][] = url_de_base(1) . str_replace("../", "", _DIR_PLUGIN_SJCYCLE) . "aide/";
?>
