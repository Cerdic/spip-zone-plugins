<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_zformart_charger_dist(){
    $valeurs = array(
        "idrubrique"  => "",
    );
return $valeurs;
}

function formulaires_zformart_verifier_dist(){
    $idrubrique   = _request('idrubrique');
        
    $erreurs = array();
    //champs obligatoire
        foreach (array ('idrubrique') as $obligatoire) {
        if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champs est obligatoire';
        }


    return $erreurs;
}

function formulaires_zformart_traiter_dist(){
    $idrubrique   = _request('idrubrique');
    $nom="zforumart";
    $impt="oui";
    
    $retour = array();
    $retour['message_ok'] = "bravo";
    //$retour['redirect'] = "spip.php?page=perdu";
//include_spip('action/editer_objet');
//$objet=meta;
//$id_objet = objet_inserer($objet);
 //$set = array (
        //'nom'    => $nom,
        //'valeur' => $idrubrique,
        //'impt'      => $impt
    //);

    //objet_modifier($objet, $id_objet, $set);

    include_spip('inc/config');
ecrire_config('zforumart/idrubrique', '$idrubrique');


    return $retour;
}
?>


