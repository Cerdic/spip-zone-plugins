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
	$contexte['paiement_panier'] = _request('paiement_panier');
	$contexte['visualisation_panier'] = _request('paiement_panier');
	$contexte['echoppe_prestataire_paiement'] = _request('prestataire_paiement');
	$contexte['finaliser_paiement'] = _request('finaliser_paiement');
	$contexte['message'] = "";
	if ($contexte['valider_panier'] == "oui"){
		$contexte['formulaire'] = "formulaires/panier_inscription";
	}
	
	if ($contexte['paiement_panier'] == "oui"){
		$contexte['formulaire'] = "formulaires/panier_paiement";
	}
	
	if ($contexte['finaliser_paiement'] == "oui"){
		session_set('echoppe_prestataire_paiement', $contexte['echoppe_prestataire_paiement'] );
		if ($GLOBALS['auteur_session']['id_auteur'] > 0){
			$contexte['formulaire'] = "formulaires/panier_prestataire_paiement";
		}else{
			$contexte['formulaire'] = "formulaires/panier_erreur_login";
		}
	}
	
	if ($contexte['validation_paiement'] == "oui"){
		$contexte['mail_corps'] = recuperer_fond('fonds/echoppe_mail_virement', array("echoppe_token_panier"=>session_get('echoppe_token_panier'),"echoppe_token_client"=>session_get('echoppe_token_client') ));
		$contexte['mail_to'] = $GLOBALS['auteur_session']['email'];
		if (lire_config('echoppe/email_pour_confirmation_panier') != ""){
			$contexte['mail_to'] .= ','.lire_config('echoppe/email_pour_confirmation_panier');
		}
		mail($contexte['mail_to'],_T('echoppe:validation_de_votre_panier'),$contexte['mail_corps']);
		spip_log('Evois du mail de confirmation','echoppe');
		$contexte['formulaire'] = "formulaires/panier_fin";
	}
	
	//var_dump($contexte);
	return array($contexte['formulaire'],0,$contexte);
	
}

?>
