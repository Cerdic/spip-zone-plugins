<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_produit_dist() {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();

        if (!preg_match(",^(\d+)$,", $arg, $r)) {
                 spip_log("action_supprimer_produit_dist $arg pas compris");
        } else {
                action_supprimer_produit_post($r[1]);
        }
}

function action_supprimer_produit_post($id_produit) {
        sql_delete("spip_amap_produits", "id_produit=" . sql_quote($id_produit));

        include_spip('inc/invalideur');
        suivre_invalideur("id='id_produit/$id_produit'");
}
?>