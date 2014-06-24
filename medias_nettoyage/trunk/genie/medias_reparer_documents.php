<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

include_spip('medias_nettoyage_fonctions');
include_spip('inc/filtres');
include_spip('inc/meta');
include_spip('inc/medias_lanceur');

function genie_medias_reparer_documents_dist ($t)
{

    // Si on est en SPIP 2, on regarde $GLOBALS
    // En SPIP 3, on passe par la fonction lire_config
    if (intval(spip_version())==2) {
        $medias_nettoyage = ($GLOBALS['meta']['medias_nettoyage'])
            ? @unserialize($GLOBALS['meta']['medias_nettoyage'])
            : null;
    } elseif (intval(spip_version()) == 3) {
        include_spip('inc/config');
        $medias_nettoyage = lire_config('medias_nettoyage');
    } else {
        $medias_nettoyage = null;
    }

    // Si le plugin n'a pas encore été configuré,
    // on lance le script entre 00h00 et 06h00
    if (!isset($medias_nettoyage)) {
        spip_log(
            _T(
                'medias_nettoyage:message_log_tranche_defaut',
                array(
                    'date' => date_format(date_create(), 'Y-m-d H:i:s'),
                    'fonction' => __FUNCTION__
                )
            ),
            "medias_nettoyage"
        );
        medias_lanceur('medias_reparer_doc_fichiers');

    } elseif (isset($medias_nettoyage['activation'])
        and $medias_nettoyage['activation'] == 'oui'
        and (!isset($medias_nettoyage['horaires'])
        or $medias_nettoyage['horaires'] == '')) {
        // Si on a activé la tranche horaire mais qu'on a pas choisi le créneau
        // On lance le script entre 00h00 et 06h00
        spip_log(
            _T(
                'medias_nettoyage:message_log_tranche_actif_horaire_undefined',
                array(
                    'date' => date_format(date_create(), 'Y-m-d H:i:s'),
                    'fonction' => __FUNCTION__
                )
            ),
            "medias_nettoyage"
        );
        medias_lanceur('medias_reparer_doc_fichiers');

    } elseif (isset($medias_nettoyage['activation'])
        and $medias_nettoyage['activation'] == 'oui'
        and isset($medias_nettoyage['horaires'])) {
        // Si on a activé la tranche horaire et qu'on a choisi le créneau
        // On lance le script dans la tranche horaire choisie
        $horaires = ($medias_nettoyage['horaires']=='') ? array(0,600) : explode('-', $medias_nettoyage['horaires']);
        spip_log(
            _T(
                'medias_nettoyage:message_log_tranche_actif_horaire_defini',
                array(
                    'date' => date_format(date_create(), 'Y-m-d H:i:s'),
                    'fonction' => __FUNCTION__,
                    'debut' => $horaires[0],
                    'fin' => $horaires[1]
                )
            ),
            "medias_nettoyage"
        );
        medias_lanceur('medias_reparer_doc_fichiers', $horaires[0], $horaires[1]);

    } elseif (isset($medias_nettoyage['activation'])
        and $medias_nettoyage['activation'] == 'non') {
        // Si on a sélectionné 'non' pour la tranche horaire,
        // on lance le script selon le timing prévu dans medias_nettoyage_pipelines.php
        spip_log(
            _T(
                'medias_nettoyage:message_log_tranche_desactivee',
                array(
                    'date' => date_format(date_create(), 'Y-m-d H:i:s'),
                    'fonction' => __FUNCTION__
                )
            ),
            "medias_nettoyage"
        );
        $medias_reparer_doc_fichiers = charger_fonction('medias_reparer_doc_fichiers', 'inc');
        $medias_reparer_doc_fichiers();
    }

    return 1;
}


?>