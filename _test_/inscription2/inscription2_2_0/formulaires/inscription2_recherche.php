<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Chargement des valeurs par defaut des champs du formulaire
 * 
 */
function formulaires_inscription2_recherche_charger_dist(){
	
	$datas['ordre'] = _request('ordre');
	$datas['desc'] = _request('desc');
	$datas['case'] = _request('case');
	$datas['valeur'] = _request('valeur');
	
	if(_request('afficher_tous')){
		set_request('valeur','');
		set_request('case','');
	}
	return $datas;
}

/**
 * 
 * Vérification du formulaire
 * @return 
 */
function formulaires_inscription2_recherche_verifier_dist(){
	
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

/**
 * 
 * Traitement du formulaire
 * @return 
 */
function formulaires_inscription2_recherche_traiter_dist(){
	
	$retour = array();
	
    return $retour;
}
?>