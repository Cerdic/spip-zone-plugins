<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion des css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function gisban_insert_head_css($flux){
    $flux .="\n".'<link rel="stylesheet" href="'. find_in_path('lib/leaflet.photon/leaflet.photon.css') .'" />';
    $flux .="\n".'<link rel="stylesheet" href="'. find_in_path('css/photon_search_gis.css') .'" />';
    return $flux;
}

/**
 * Insertion des scripts et css du plugin dans les pages de l'espace privé
 *
 * @param $flux
 * @return mixed
 */
function gisban_header_prive($flux){
    $flux .= gisban_insert_head_css('');
    return $flux;
}
?>
