<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function action_htmlminifier_appliquer_options_dist() {

    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();
    if(empty($arg) || !is_string($arg)) {
        $arg = 'super_safe';
    }

    $options = HTMLMinifier::get_presets($arg);

    ecrire_config('htmlminifier', $options);

    if ($redirect = _request('redirect')) {
        include_spip('inc/headers');
        $redirect = parametre_url($redirect, 'message_ok_config', 'oui');
        redirige_par_entete($redirect);
    }

}