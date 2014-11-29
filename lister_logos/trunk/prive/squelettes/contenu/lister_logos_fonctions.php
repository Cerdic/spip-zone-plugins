<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/chercher_logo');
include_spip('base/objets');

function logo_infos($fichier, $index = null)
{
    // Fonction one ne peut plus simple.
    preg_match("/\/(\w+)(on|off)(\d+).(\w+)$/", $fichier, $r);
    if (isset($index) and intval($index)) {
        return $r[$index];
    }
    return $r;
}

function logo_etat($fichier)
{
    $infos = logo_infos($fichier);

    if ($infos[2] == 'on') {
        return _T('lister_logos:logo_normal');
    } else {
        return _T('ecrire:logo_survol');
    }

}
?>