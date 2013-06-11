<?php

function formulaires_erreurs_js_traiter_dist () {

    $erreur = _request('data');
    spip_log(json_decode($erreur, 'js_client'));
    return;
}

?>