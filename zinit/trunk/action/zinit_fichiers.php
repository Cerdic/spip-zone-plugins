<?php

function action_zinit_fichiers_dist($arg = null)
{
    if (is_null($arg)) {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }
    $arg = explode('/', $arg);
    if ($arg[0] == 'oui') {
        if (isset($arg[1]) and $arg[1] == 'zinit_dir_squelettes') {
            $cible = _ZINIT_DIR_SQUELETTES;
        } elseif (isset($arg[1]) and $arg[1] == 'dir_squelettes') {
            $cible = _DIR_SQUELETTES;
        } else {
            $cible = _ZINIT_DIR_SQUELETTES;
        }
        spip_log(print_r($arg, true), 'zinit');
        include_spip('zinit_fonctions');
        // On crée d'abord les répertoires
        $repertoires = zinit_repertoire_skel_creer($cible);
        // On crée les fichiers pour chaque objet
        $fichiers = zinit_template_skel_creer($cible);

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
