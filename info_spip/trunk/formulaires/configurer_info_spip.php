<?php

/*
* Plugin Info SPIP
* (c) 2014 Teddy Payet
* Distribue sous licence GPL
*
*/

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

include_spip('inc/config');
include_spip('inc/meta');

function formulaires_configurer_info_spip_charger_dist()
{
    $valeurs = array();

    if (lire_config('info_spip')) {
        $valeurs = lire_config('info_spip');
    }
    return $valeurs;
}

function formulaires_configurer_info_spip_verifier_dist()
{
    $erreurs = array();
    foreach (array('cle', 'actif') as $obligatoire) {
        if (!_request($obligatoire)) {
            $erreurs[$obligatoire] = _T('info_obligatoire');
        }
    }
    return $erreurs;
}

function formulaires_configurer_info_spip_traiter_dist()
{
    $res = array();

    $res['cle']     = _request('cle');
    $res['actif']   = _request('actif');

    if (ecrire_meta('info_spip', @serialize($res), 'non')) {
        $res['message_erreur'] = _T('info_spip:enregistrement_ko');
    } else {
        $res['message_ok'] = _T('info_spip:enregistrement_ok');
    }

    return $res;
}

?>