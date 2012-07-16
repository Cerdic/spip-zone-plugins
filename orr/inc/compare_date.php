<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function compare_date($date,$id){
        if ($result = sql_select(
                                array(
                                "reservation.orr_date_debut",
                                "reservation.orr_date_fin"),
                                array(
                                "spip_orr_reservations AS reservation",
                                "spip_orr_reservations_liens AS lien",
                                "spip_orr_ressources AS ressource"),
                                array(
                                "reservation.id_orr_reservation=lien.id_orr_reservation",
                                "ressource.id_orr_ressource=lien.id_objet",
                                "lien.objet='orr_ressource'",
                                "ressource.id_orr_ressource=$id")
                                )){
                                    while ($r = sql_fetch($result)){
                                        if (($r[orr_date_debut]<=$date)and($date<=$r[orr_date_fin])){
                                            $retour=2;
                                            break;
                                        }
                                        if (($r[orr_date_debut]>=$date)and($date<=$r[orr_date_fin])){
                                            $retour=1;
                                            break;
                                        }
                                        if (($r[orr_date_debut]<=$date)and($date>=$r[orr_date_fin])){
                                            $retour=3;
                                            break;
                                        }
                                    }
        }
    return $retour;
}
?>
