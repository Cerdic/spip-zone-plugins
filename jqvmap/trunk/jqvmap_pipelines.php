<?php

/**
 * Utilisations de pipelines par jQuery Vector Maps.
 *
 * @plugin     jQuery Vector Maps
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function jqvmap_header_prive($flux)
{
    $css = find_in_path('lib/jqvmap/jqvmap/jqvmap.css');
    $flux = $flux."\n<link href='$css' media='screen' rel='stylesheet' type='text/css' />\n";
    $js = find_in_path('lib/jqvmap/jqvmap/jquery.vmap.js');
    $flux = $flux."\n<script type='text/javascript' src='$js'></script>\n";

    return $flux;
}
