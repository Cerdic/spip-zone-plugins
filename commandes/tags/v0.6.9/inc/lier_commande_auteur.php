<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

    function inc_lier_commande_auteur_dist($id_commande,$id_auteur) {

        if (intval($id_commande) && intval($id_auteur)) {
            $res = sql_updateq(
                'spip_commandes',
                array(
                    'id_auteur' => $id_auteur
                ),
                'id_commande = '.$id_commande
            );
        }
    }
?>
