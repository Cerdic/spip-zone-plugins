<?php

/**
 * Plugin Factures pour SPIP 2.1
 * Auteur : C.MARION - Ateliers CYM
 **/

function factures_insert_head($flux){
    $flux .= '<!-- insertion de la css factures --><link rel="stylesheet" type="text/css" href="'.find_in_path('css/factures.css').'" media="all" />';
    return $flux;
}
