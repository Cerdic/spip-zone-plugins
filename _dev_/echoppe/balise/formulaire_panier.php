<?php
function balise_FORMULAIRE_PANIER($p){
	return calculer_balise_dynamique($p, 'FORMULAIRE_PANIER', array('id_panier'));
}

function balise_FORMULAIRE_PANIER_stat($args, $filtres) {
	if(!$args[1]) {
		$args[1]='formulaire_panier';
	}
	return (array($args[0],$args[1]));
}

function balise_FORMULAIRE_PANIER_dyn($id_panier, $formulaire) {
	
	$contexte = array();
	$contexte['id_panier'] = _request('id_panier');
	$contexte['editer_produit'] = _request('editer_produit');
	$contexte['formulaire'] = "formulaires/panier"; 
	$contexte['valider_panier'] = _request('valider_panier');
	$contexte['paiment_panier'] = _request('paiement_panier');
	$contexte['visualisation_panier'] = _request('paiement_panier');
	$contexte['message'] = "";
	
	if ($contexte['valider_panier'] == "oui"){
		if (!(empty($GLOBALS['auteur_session']))){
			$contexte['formulaire'] = "formulaires/panier_validation";
		}else{
			$contexte['formulaire'] = "formulaires/panier_inscription";
		}
	}
	
	if ($contexte['paiement_panier'] == "oui"){
		$corps_mail = recuperer_fond('fonds/echoppe_mail_virement', array("echoppe_token_panier"=>session_get('echoppe_token_panier'),"echoppe_token_client"=>session_get('echoppe_token_client') ));
		
	}
	
	
	return array($contexte['formulaire'],0,$contexte);
	
}

?>
