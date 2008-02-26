<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_OPENPUBLISHING',(_DIR_PLUGINS.end($p)));

//
// Definition des champs extra pour les articles openPublishing
//

$GLOBALS['champs_extra'] = Array (

        'articles' => Array (
                        "OP_pseudo" => "ligne|brut|pseudonyme du r&eacute;dacteur",
                        "OP_mail" => "ligne|propre|mail du r&eacute;dacteur"
                )
        );
?>