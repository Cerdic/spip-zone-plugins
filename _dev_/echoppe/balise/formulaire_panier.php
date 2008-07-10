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
	$contexte['id_auteur'] = lire_meta('id_auteur');
	$contexte['editer_produit'] = _request('editer_produit');
	$contexte['formulaire'] = "formulaires/panier"; 
	$contexte['valider_panier'] = _request('valider_panier');
	$contexte['paiement_panier'] = _request('paiement_panier');
	$contexte['visualisation_panier'] = _request('paiement_panier');
	$contexte['echoppe_prestataire_paiement'] = _request('prestataire_paiement');
	$contexte['finaliser_paiement'] = _request('finaliser_paiement');
	$contexte['retour_validation'] = _request('retour_validation');
	$contexte['token_panier'] = session_get('echoppe_token_panier');
	$contexte['message_erreur'] = "";
	
	$_sql = "SELECT id_produit FROM spip_echoppe_paniers WHERE token_panier = '".session_get('echoppe_token_panier')."';";
	$_res = spip_query($_sql);
	$_quantite = spip_num_rows($_res);
	zero_si_vide($_quantite);
	$contexte['total_item_panier'] = $_quantite;
	
	
	if (session_get('echoppe_statut_panier') != "temp" && session_get('echoppe_statut_panier') != "valide" ) $contexte['message_erreur'] = _T('echoppe:votre_panier_est_en_cour_de_paiement_vous_ne_pouvez_plus_ajouter_de_produit_a_ce_stade').' <a href="'.generer_url_action('echoppe_reinit_panier').'">'._T('echoppe:reinitialiser_mon_panier').'</a>';
	
	if ($contexte['valider_panier'] == "oui"){
		$contexte['formulaire'] = "formulaires/panier_inscription";
	}
	
	if ($contexte['paiement_panier'] == "oui"){
		if ($contexte['total_item_panier'] > 0){
			spip_log("passage du panier ".$contexte['token_panier']." en mode reserve", "echoppe");
			session_set('echoppe_statut_panier', 'reserve' );
			$sql_update_reserve = "UPDATE spip_echoppe_paniers SET statut='reserve' WHERE token_panier = '".$contexte['token_panier']."';";
			$res_update_reserve = spip_query($sql_update_reserve);
			$contexte['formulaire'] = "formulaires/panier_paiement";
		}else{
			$contexte['message_erreur'] = _T('echoppe:votre_panier_est_vide_on_ne_peu_continuer_la_validation');
		}
	}
	
	if ($contexte['finaliser_paiement'] == "oui"){
		if ($contexte['total_item_panier'] > 0){
			spip_log("passage du panier ".$contexte['token_panier']." en mode valide", "echoppe");
			session_set('echoppe_statut_panier', 'valide' );
			$sql_update_valide = "UPDATE spip_echoppe_paniers SET statut='valide' WHERE token_panier = '".$contexte['token_panier']."';";
			$res_update_valide = spip_query($sql_update_valide);
			session_set('echoppe_prestataire_paiement', $contexte['echoppe_prestataire_paiement'] );
			if ($GLOBALS['auteur_session']['id_auteur'] > 0){
				$contexte['formulaire'] = "formulaires/panier_prestataire_paiement";
			}else{
				$contexte['formulaire'] = "formulaires/panier_erreur_login";
			}
		}else{
			$contexte['message_erreur'] = _T('echoppe:votre_panier_est_vide_on_ne_peu_continuer_la_validation');
		}
	}
	
	if ($contexte['retour_validation'] == "reussi" ){
		if ($contexte['total_item_panier'] > 0){
			spip_log("passage du panier ".$contexte['token_panier']." en mode paye", "echoppe");
			session_set('echoppe_statut_panier', 'paye' );
			$sql_update_paye = "UPDATE spip_echoppe_paniers SET statut='paye' WHERE token_panier = '".$contexte['token_panier']."';";
			$res_update_paye = spip_query($sql_update_paye);
			
			$contexte['mail_corps'] = recuperer_fond('fonds/echoppe_mail_virement', array("echoppe_token_panier"=>session_get('echoppe_token_panier'),"echoppe_token_client"=>session_get('echoppe_token_client') ));
			
			$contexte['mail_from'] = lire_meta('email_webmaster').' ('.extraire_multi(lire_meta('nom_site')).')';
			
			$contexte['headers'] = "From:".$contexte['mail_from']."\r\n";
			
			$contexte['sql_mail_client'] = "SELECT a.email FROM spip_auteurs a, spip_echoppe_client b WHERE b.token_client='".session_get('echoppe_token_client')."' AND a.id_auteur=b.id_auteur;";
			$contexte['res_mail_client'] = spip_query($contexte['sql_mail_client']);
			$contexte['mail_client'] = spip_fetch_array($contexte['res_mail_client']);
			$contexte['email_client'] = $contexte['mail_client']["email"];
			
			
			$contexte['mail_to'] = $contexte['email_client'];
			if (lire_config('echoppe/email_pour_confirmation_panier') != ""){
				$contexte['mail_to'] .= ','.lire_config('echoppe/email_pour_confirmation_panier');
			}
			
			if (lire_meta('email_webmaster') != ""){
				$contexte['mail_to'] .= ','.lire_meta('email_webmaster');
			}
			
			//spip_log('envois d\'un mail de confirmation a '.$contexte['mail_to'].' '.$contexte['email_client'].' '.$contexte['headers'],'echoppe');
			
			
			mail($contexte['mail_to'],_T('echoppe:validation_de_votre_panier'),$contexte['mail_corps'],$contexte['headers']);
			spip_log('Evois du mail de confirmation','echoppe');
			$contexte['formulaire'] = "formulaires/panier_fin";
			
			$test_existance_token = 0;
			$new_token = md5(uniqid(rand(), true));
			while ($test_existance_token > 0){
				$new_token = md5(uniqid(rand(), true));
				$test_existance_token = spip_num_rows(spip_query("SELECT id_panier FROM spip_echoppe_paniers WHERE token_panier = '".$new_token."' ;"));
			}
			session_set('echoppe_token_panier', $new_token );
			
			session_set('echoppe_statut_panier', 'temp' );
			
		}else{
			$contexte['message_erreur'] = _T('echoppe:votre_panier_est_vide_on_ne_peu_continuer_la_validation');
		}
		
	}
	
	return array($contexte['formulaire'],0,$contexte);
	
}

?>
