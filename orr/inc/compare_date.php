<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

//$date=$_REQUEST["date"];
//$id=1;

function compare_date($date,$id)
{
    if ($result = sql_select("orr_date_debut, orr_date_fin","spip_orr_reservations","id_orr_ressource=$id")){
        while ($r = sql_fetch($result)){
            if (($r[orr_date_debut]<$date)and($date<$r[orr_date_fin])){
                $retour=1;
                break;
            }
            else $retour=0;
        }
    }
    return $retour;
}
?>
