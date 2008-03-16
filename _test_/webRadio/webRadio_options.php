<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_WEBRADIO',(_DIR_PLUGINS.end($p)));

// ajout d'un champs playlist(oui,non) dans la table spip_documents
include_spip ('base/serial');
$GLOBALS['tables_principales']['spip_documents']['field']['playlist'] = "ENUM('oui', 'non') NOT NULL DEFAULT 'non'";
?>