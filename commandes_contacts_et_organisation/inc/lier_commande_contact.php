<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

    function inc_lier_commande_contact_dist($id_commande,$id_contact) {

        if (intval($id_commande) && intval($id_contact)) {

            $res = sql_updateq(
                'spip_contacts_liens',
                array(
                    'id_contact' => $id_contact,
                    'objet' => 'commande',
                    'id_objet' => $id_commande
                ),
                'id_objet = '.$id_commande.' AND objet = "commande"'
            );

            if (!$res) {
                $res = sql_insertq(
                    'spip_contacts_liens',
                    array(
                        'id_contact' => $id_contact,
                        'objet' => 'commande',
                        'id_objet' => $id_commande
                    )
                );
            }
        }
    }

?>
