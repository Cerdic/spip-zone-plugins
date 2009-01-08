<?php

function formulaires_panier_charger_dist(){
	$valeurs = array(
				'editer_panier'=>_request('editer_panier'),
				'id_panier'=>_request('id_panier'),
				'message'=>''
	);
	
	return $valeurs;
}

function formulaires_panier_verifier_dist(){
	$erreurs = array();
	
	if (_request('editer_panier'))
		$erreurs['editer_panier'] = 'oui';	
		
	if (count($erreurs))
		$erreurs['message_erreur'] = _T('echoppe:votre_saisie_contient_des_erreurs');
		


	return $erreurs;
}

function formulaires_panier_traiter_dist(){
	$quantite = Array();
	$quantite = _request('quantite');
	
	$messages = Array();
	
	include_spip('inc/echoppe_panier');
	
	if (echoppe_panier_autoriser_modification(session_get('echoppe_token_panier'))){
		
		foreach($quantite as $id_produit => $quantite){
			
			spip_log('produit '.$id_produit.' modifie pour '.$quantite,'echoppe');
			
			echoppe_panier_modifier_quantite_produit($id_produit,$quantite,session_get('echoppe_token_panier'));
		}
		
		$messages['message_ok'] = _T('echoppe:modification_du_panier_ok');
		
	}else{
		
		$messages['message_erreur'] = _T('echoppe:modification_du_panier_non_permise');
		spip_log(_T('echoppe:modification_du_panier_non_permise'),'echoppe');
		
	}
	
	return $messages;
}

?>
