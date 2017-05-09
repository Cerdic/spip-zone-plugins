<?php
/**
 * Fichier de configurer du Plugin SPIP
 *
 * @plugin     Info SPIP
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_SPIP\Configurer
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/config');
include_spip('inc/meta');

function formulaires_configurer_info_spip_charger_dist ()
{
    $valeurs = array();

    if (lire_config('info_spip')) {
        $valeurs = lire_config('info_spip');
    }
    return $valeurs;
}

function formulaires_configurer_info_spip_verifier_dist ()
{
    $erreurs = array();

    if (_request('actif') == 'oui' and !_request('cle')) {
        $erreurs['cle'] = _T('info_obligatoire');
    }
    return $erreurs;
}

function formulaires_configurer_info_spip_traiter_dist ()
{
    $res = array();

    $res['type_site'] = _request('type_site');
    $res['modules'] = _request('modules');
    $res['bo_url'] = _request('bo_url');
    $res['actif'] = _request('actif');
    $res['cle'] = _request('cle');

    if (ecrire_meta('info_spip', @serialize($res), 'non')) {
        $res['message_erreur'] = _T('info_spip:enregistrement_ko');
    } else {
        $res['message_ok'] = _T('info_spip:enregistrement_ok');
    }

    return $res;
}
?>
