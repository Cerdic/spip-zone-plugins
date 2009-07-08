<?php

function formulaires_paiement_charger_dist(){
	
	$valeurs = array();
	$valeurs['id_auteur'] = $GLOBALS['META']['id_auteur'];
	
	include_spip('inc/echoppe_paiement');
	if (!echoppe_valider_informations_livraison($valeurs['id_auteur'])){
		$valeurs['information_livraison_manquantes'] = _T('echoppe:impossible_d_effectuer_le_paiement_vos_infos_de_livraison_sont_manquantes_ou_incomplete_cliquez_ici_pour_les_completer', array('url_profile' => generer_url_public('echoppe_profile')));
		spip_log('ECHOPPE_INFO : infos de livraison pour paiement incomplète','echoppe');
	}
	
	if (!echoppe_valider_informations_facturation()){
		$valeurs['echoppe_non_configure'] = _T('echoppe:impossible_d_effectuer_le_paiement_echoppe_n_est_pas_completement_configure');
		spip_log('ECHOPPE_ERROR : configuration pour paiement incomplète','echoppe');
	}
	
	return $valeurs;
}


function formulaires_paiement_verifier_dist(){
	
	$erreurs = array();
	$contexte = array();
	
	$contexte['id_auteur'] = _request('id_auteur');
	include_spip('inc/echoppe_paiement');
	$valid_livraison = echoppe_valider_informations_livraison($contexte['id_auteur']);
	
	if (echoppe_valider_informations_livraison($contexte['id_auteur'])){
		foreach($valid_livraison as $key => $value){
			$erreurs['information_livraison_manquantes'] .= $value;
		}
		$erreurs['information_livraison_manquantes'] .= _T('echoppe:impossible_d_effectuer_le_paiement_vos_infos_de_livraison_sont_manquantes_ou_incomplete_cliquez_ici_pour_les_completer', array('url_profile' => generer_url_public('echoppe_profile')));
		spip_log('ECHOPPE_INFO : infos de livraison pour paiement incomplète','echoppe');
	}
	
	if (echoppe_valider_informations_facturation()){
		$erreurs['echoppe_non_configure'] = _T('echoppe:impossible_d_effectuer_le_paiement_echoppe_n_est_pas_completement_configure');
		spip_log('ECHOPPE_ERROR : configuration pour paiement incomplète','echoppe');
	}
	
	return $erreurs;
}


function formulaires_paiement_traiter_dist(){
	
	$messages = array();
	$messages['etape_paiement'] = _request('etape_paiement');
	
	switch ($messages['etape_paiement']) {
		
		case 'selection_prestataire_paiement' :
			$modele = sql_fetsel("modele","spip_echoppe_prestataires","id_prestataire = '"._request('id_prestataire')."'");
			$modele= str_replace('../','',$modele);
			$modele= str_replace('.html','',$modele);
			session_set('echoppe_modele_prestataire_paiement', 'prestataires/paiement/'.$modele['modele'] );
		break;

		case 'recapitulatif_commande' :
		
		break;

		case 'retour_prestataire_ok' :
			$messages['paiement_ok_termine'] = _T('echoppe:confirmation_bonne_reception_du_paiement_commande_envoyee-dans_les_plus_bref_delais');
			
		break;
		
		case 'retour_prestataire_no_ok' :
			
			
		break;
		
		default :
			
		break;
	}
	

	return $messages;
}

?>
