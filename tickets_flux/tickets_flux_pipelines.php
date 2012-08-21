<?php
/**
 * Plugin Tickets Flux
 *
 */

function tickets_flux_insert_head($flux){

    $flux .= '<!-- insertion de la css tickets_flux--><link rel="stylesheet" type="text/css" href="'.find_in_path('tickets_flux.css').'" media="all" />';

    return $flux;
}
?>
