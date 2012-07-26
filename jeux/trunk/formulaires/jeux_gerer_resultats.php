<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_jeux_gerer_resultats_charger($param=array()){
    return $param;
}
function formulaires_jeux_gerer_resultats_verifier($param=array()){
    $erreurs = array();
    if (!_request('confirmer')){
        $erreurs['non_confirme']=true;
    }
    return $erreurs;
}
function formulaires_jeux_gerer_resultats_traiter($param=array()){
    return $param;
}
?>