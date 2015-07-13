<?php

function action_zi_maj_fichiers_dist($arg = null)
{
    if (is_null($arg)) {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }
    if ($arg == 'oui') {
        include_spip('zcore_init_fonctions');
        // On crée d'abord les répertoires
        $repertoires = zi_repertoire_skel_creer();
        // On crée les fichiers pour chaque objet
        $fichiers = zi_template_skel_creer();

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
