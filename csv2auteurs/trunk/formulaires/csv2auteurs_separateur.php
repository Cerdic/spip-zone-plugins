<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_csv2auteurs_separateur_charger_dist(){
    $valeurs = array(
        "separateur"  => lire_config("csv2auteurs_separateur"),
    );
return $valeurs;
}

function formulaires_csv2auteurs_separateur_verifier_dist(){
        
    $erreurs = array();
    //champs obligatoire
    if (!_request(separateur)) $erreurs['separateur'] = _T('csv2auteurs:obligatoire');
    return $erreurs;
}

function formulaires_csv2auteurs_separateur_traiter_dist(){
    $separateur   = _request('separateur');
    
    ecrire_meta("csv2auteurs_separateur",_request('separateur'));
    $retour = array();
    $retour['message_ok'] = _T('csv2auteurs:nouveau_separateur',array("separateur"=>$separateur));
    //$retour['redirect'] = "ecrire/?exec=csv2auteurs";

    return $retour;
}
?>
