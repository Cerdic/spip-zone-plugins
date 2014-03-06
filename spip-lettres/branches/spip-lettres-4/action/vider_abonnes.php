<?php

/**
 * Supprime tous les abonnés d'une rubrique
 * ce qui permet de réimporter une liste d'abonnés pour cette rubrique
 * tout en conservant les informations de désabonnement
 */

function action_vider_abonnes_dist() {
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();
    $id_rubrique = intval($arg);

    include_spip('inc/autoriser');
    if (autoriser('vider_abonnes', $id_rubrique)) {

        // suppression des abonnés
        sql_delete('spip_abonnes', sql_in_select('id_abonne', 'id_abonne', 'spip_abonnes_rubriques', 'id_rubrique=' . sql_quote($id_rubrique)));
        /*
         DELETE
         FROM spip_abonnes
         WHERE id_abonne
         IN (SELECT id_abonne
         FROM `spip_abonnes_rubriques`
         WHERE id_rubrique = xxx)
         */

        // suppression des abonnements (liens avec la rubrique)
        sql_delete('spip_abonnes_rubriques', 'id_rubrique=' . sql_quote($id_rubrique));
        /*
         DELETE FROM spip_abonnes_rubriques
         WHERE id_rubrique = xxx
         */

        spip_log('Suppression des abonnés à la rubrique $id_rubrique', 'spip_lettres');
    }
    return;
}