<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_lieu_dist() {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();

        if (!preg_match(",^(\d+)$,", $arg, $r)) {
                 spip_log("action_supprimer_lieu_dist $arg pas compris");
        } else {
                action_supprimer_lieu_post($r[1]);
        }
}

function action_supprimer_lieu_post($id_lieu) {
        sql_delete("spip_amap_lieux", "id_lieu=" . sql_quote($id_lieu));

        include_spip('inc/invalideur');
        suivre_invalideur("id='id_lieu/$id_lieu'");
}
?>