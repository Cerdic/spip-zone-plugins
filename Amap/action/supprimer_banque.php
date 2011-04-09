<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_banque_dist() {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();

        if (!preg_match(",^(\d+)$,", $arg, $r)) {
                 spip_log("action_supprimer_banque_dist $arg pas compris");
        } else {
                action_supprimer_banque_post($r[1]);
        }
}

function action_supprimer_banque_post($id_banque) {
        sql_delete("spip_amap_banques", "id_banque=" . sql_quote($id_banque));

        include_spip('inc/invalideur');
        suivre_invalideur("id='id_banque/$id_banque'");
}
?>