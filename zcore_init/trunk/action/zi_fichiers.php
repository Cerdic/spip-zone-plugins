<?php

function action_zi_fichiers_dist($arg = null)
{
    if (is_null($arg)) {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }
    $arg = explode('/', $arg);
    if ($arg[0] == 'oui') {
        if (isset($arg[1]) and $arg[1] == 'zi_dir_squelettes') {
            $cible = _ZI_DIR_SQUELETTES;
        } elseif (isset($arg[1]) and $arg[1] == 'dir_squelettes') {
            $cible = _DIR_SQUELETTES;
        } else {
            $cible = _ZI_DIR_SQUELETTES;
        }
        spip_log(print_r($arg, true), 'zcore_init');
        include_spip('zcore_init_fonctions');
        // On crée d'abord les répertoires
        $repertoires = zi_repertoire_skel_creer($cible);
        // On crée les fichiers pour chaque objet
        $fichiers = zi_template_skel_creer($cible);

        if ($repertoires and $fichiers) {
            if (!$redirect = _request('redirect')) {
                $redirect = parametre_url(generer_url_ecrire('zcore_skel'));
            }
            $redirect = str_replace('&amp;', '&', urldecode($redirect));
            include_spip('inc/headers');
            redirige_par_entete($redirect);
        }
    }

    return false;
}
