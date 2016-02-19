<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function soapsympa_autoriser(){}

function autoriser_configurer_soapsympa_bouton_dist($faire, $type, $id, $qui, $opt) {
return ($qui['statut'] == '0minirezo');
}


function autoriser_edition_listes_bouton_dist($faire, $type, $id, $qui, $opt) {
        return ($qui['statut'] == '0minirezo');
    }

function autoriser_edition_listes_dist($faire, $type, $id, $qui, $opt) {
        return ($qui['statut'] == '0minirezo');
    }

function autoriser_gerer_abonnements_dist($faire, $type, $id, $qui, $opt) {
        return ($qui['statut'] == '0minirezo');
    }


?>