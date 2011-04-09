<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_type_contrat_dist() {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();

        if (!preg_match(",^(\d+)$,", $arg, $r)) {
                 spip_log("action_supprimer_type_contrat_dist $arg pas compris");
        } else {
                action_supprimer_saison_post($r[1]);
        }
}

function action_supprimer_saison_post($id_type_contrat) {
        sql_delete("spip_amap_types_contrats", "id_type_contrat=" . sql_quote($id_type_contrat));

        include_spip('inc/invalideur');
        suivre_invalideur("id='id_type_contrats/$id_type_contrats'");
}
?>