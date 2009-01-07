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

	if (count($erreurs))
		$erreurs['message_erreur'] = _T('echoppe:votre_saisie_contient_des_erreurs');
	
	return $erreurs;
}

function formulaires_panier_traiter_dist(){
	$quantite = Array();
	$quantite = _request('quantite');
	
	foreach($quantite as $key => $value){
		spip_log('produit '.$key.' modifie pour '.$value,'echoppe');
	}
	
	return array(
			'message_ok'=> _T('echoppe:modification_du_panier_ok')
			);
}

?>
