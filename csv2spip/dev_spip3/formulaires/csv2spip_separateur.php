<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_csv2spip_separateur_charger_dist(){
    $valeurs = array(
        "separateur"  => lire_config("csv2spip_separateur","§"),
    );
return $valeurs;
}

function formulaires_csv2spip_separateur_verifier_dist(){
        
    $erreurs = array();
    //champs obligatoire
    if (!_request(separateur)) $erreurs['separateur'] = _T('csv2spip:obligatoire');
    return $erreurs;
}

function formulaires_csv2spip_separateur_traiter_dist(){
    $separateur   = _request('separateur');
    
    ecrire_meta("csv2spip_separateur",_request('separateur'));
    $retour = array();
    $retour['message_ok'] = _T('csv2spip:nouveau_separateur',array("separateur"=>$separateur));
    //$retour['redirect'] = "ecrire/?exec=csv2spip";

    return $retour;
}
?>
