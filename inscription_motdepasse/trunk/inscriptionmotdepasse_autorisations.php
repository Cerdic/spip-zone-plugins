<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function inscriptionmotdepasse_autoriser(){}


function autoriser_authentifierauteur($faire, $quoi, $id, $qui, $opt) {
    $statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur=' . intval($id));
    if ( $statut == '5poubelle' or $statut == 'nouveau' ) return false;
    return true;
}

