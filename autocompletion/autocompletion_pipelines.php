<?php
// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function autocompletion_header_prive($flux){
    $flux = autocompletion_insert_head($flux);
    return $flux;
}

function autocompletion_jquery_plugins($scripts){
    $scripts[] = "javascript/jquery-ui-1.8.21.custom.min.js";
    return $scripts;
}

function autocompletion_insert_head($flux){
    $css = find_in_path('css/autocompletion.css');
    $flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";
    $flux .= "\n<link rel='stylesheet' type='text/css' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css' />\n";
    $flux .= "\n<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=true'></script>\n";
    return $flux;
}
