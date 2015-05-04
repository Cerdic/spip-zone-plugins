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


/**
 * Ajouter le module leaflet.photon.js dans les javascripts chargés
 *
 * @param $flux
 * @return mixed
 */
function gisban_recuperer_fond($flux){
    if ($flux['args']['fond'] == 'modeles/carte_gis') {
        $modele = recuperer_fond('inclure/inc-carte-gisban', $flux['data']['contexte']);
        $flux['data']['texte'] .= "\n" . $modele;
    }
    if ($flux['args']['fond'] == 'javascript/gis.js') {
        $ajouts = "var filter_gisban = '". lire_config('gisban/filtre_gisban') ."';\n";
        $ajouts .= "\n" . spip_file_get_contents(find_in_path('lib/leaflet.photon/leaflet.photon.js'));
        $flux['data']['texte'] .= $ajouts;
    }
    return $flux;
}
?>
