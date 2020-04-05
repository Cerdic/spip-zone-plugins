<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_fusionmots_charger_dist(){
    return array("remplacer"=>array(),'par'=>'');
}

function formulaires_fusionmots_verifier_dist(){
    $erreurs    = array();
    
    if (!_request('remplacer')){
        $erreurs['remplacer']   =_T('fusionmots:erreur_remplacer');   
    }
    
    if (!_request('par')){
        $erreurs['par']         =_T('fusionmots:erreur_par');   
    }
    return $erreurs;        
}

function formulaires_fusionmots_traiter_dist(){
	$remplacer 	= _request('remplacer');
	$par		= _request('par');
	include_spip('inc/fusionner_mots');
    fusionner_mots($remplacer,$par);
    return array();
}
?>