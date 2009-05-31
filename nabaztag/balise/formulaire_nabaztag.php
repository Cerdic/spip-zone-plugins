<?php

if (!defined("_ECRIRE_INC_VERSION")) return;    #securite

function balise_FORMULAIRE_NABAZTAG($p) {
    return calculer_balise_dynamique($p, 'FORMULAIRE_NABAZTAG', array());
}

function balise_FORMULAIRE_NABAZTAG_stat($args, $filtres) {
    return $args;
}

function balise_FORMULAIRE_NABAZTAG_dyn() {
    return array(
        'formulaires/nabaztag', 
        0, 
        array(
        )
    );
}

?>
