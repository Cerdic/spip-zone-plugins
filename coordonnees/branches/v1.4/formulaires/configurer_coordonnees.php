<?php

function formulaires_configurer_coordonnees_charger_dist(){
    $config_coordonnees = unserialize( $GLOBALS['meta']['coordonnees'] );
    return $config_coordonnees;
}

function formulaires_configurer_coordonnees_traiter_dist(){
    $res = array();
    ecrire_meta('coordonnees', serialize(array('objets'=>_request('objets'))) );
    $res['message_ok'] = _T('ecrire:config_info_enregistree'); // non verifie...
    return $res;
}

?>