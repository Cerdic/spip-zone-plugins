<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Chargement des valeurs par defaut des champs du formulaire
 * 
 * @return array L'ensemble des champs et de leur valeurs
 * @param int $id_auteur[optional] Si cette valeur est utilisée, on entre dans le cadre de
 * la modification d'un auteur, et plus dans la création
 */
function formulaires_commander_charger_dist($id_auteur = NULL){
	$champs = array();

include_spip('inc/acces');
$hash = creer_uniqid();	
$champs['hash'] = $hash ;			
$champs['id_auteur'] = $id_auteur ;			
$champs['abonnement'] = '' ;
$champs['reabo'] = '' ;			
			

if(_request('reachat')){	
	$champs['id_article'] = _request('id_article') ;				
}

		
	return $champs;
}

function formulaires_commander_verifier_dist($id_auteur = NULL){
   
	//initialise le tableau des erreurs
	$erreurs = array();
	
	if(!_request('abonnement') OR !_request('id_auteur') )
	   	$erreurs['abonnement_pas_content'] = "oui";

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_commander_traiter_dist($id_auteur = NULL){
	spip_log('traiter','paiement-form');
	global $tables_principales;
	
	$hash = _request('hash');

if($id_abonnement = _request('abonnement') AND $id_auteur = _request('id_auteur') ){	
	// enregistrer l'abonnement
	// attention aux doublons...
	sql_query("INSERT INTO `spip_auteurs_elargis_abonnements` (id_auteur, id_abonnement, hash) VALUES ('$id_auteur', "._q($id_abonnement).","._q($hash).")");
	
	$var_user['zones'] = lire_config('inscription2/zones');
	if(is_array($var_user['zones'])){
			foreach($var_user['zones'] as $value)
				sql_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`) VALUES ('$id_auteur', '$value')");
	}
	
	$message = " ";
	
	return array('editable'=>$editable,'message' => $message);

}

// a maj
if($value = _request('reachat_valide_hash')){	
$value = $id_article ;	
sql_query("INSERT INTO `spip_auteurs_elargis_articles` (`id_auteur`, `id_article`, `statut_paiement` , `hash`) VALUES ('$id_article', "._q($value).", 'a_confirmer','"._request('hash')."')");
}

	
    return array('editable'=>$editable,'message' => $message);
}
?>