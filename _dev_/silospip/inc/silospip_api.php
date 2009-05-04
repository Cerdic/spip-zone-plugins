<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip ("inc/utils");
include_spip ("inc/filtres");    /* email_valide() */
include_spip ("inc/acces");      /* creer_uniqid() */
include_spip('inc/charsets');

include_spip('base/abstract_sql');

// include_spip('inc/spiplistes_api_abstract_sql');
include_spip('inc/spiplistes_api_globales');

//CP-20080508: renvoie OK ou ERR entre crochet
// sert principalement pour les log
function silospip_str_ok_error ($statut) {
        return("[".(($statut != false) ? "OK" : "ERR")."]");
}

?>
