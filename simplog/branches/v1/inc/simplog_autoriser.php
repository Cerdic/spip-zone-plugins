<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


// declarer la fonction du pipeline
function simplog_autoriser(){}


// Seul admin a acces aux logs
function autoriser_simplog_voir_dist($faire, $type, $id, $qui, $opt) {
    //spip_log ( 'autoriser_simplog_voir_dist'.$qui['statut'], 'simplog');
    return ($qui['statut'] == '0minirezo');
}

// affichage bouton
function autoriser_simplog_bt_dist($faire, $type, $id, $qui, $opt) {
    return autoriser('voir','simplog');
}

?>
