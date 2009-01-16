<?php

function formulaires_paiement_charger_dist(){
	$valeurs = array();
	include_spip('inc/echoppe_paiement');
	if (!echoppe_valider_informations_livraison()){
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
	
	include_spip('inc/echoppe_paiement');
	
	if (!echoppe_valider_informations_livraison()){
		$erreurs['information_livraison_manquantes'] = _T('echoppe:impossible_d_effectuer_le_paiement_vos_infos_de_livraison_sont_manquantes_ou_incomplete_cliquez_ici_pour_les_completer', array('url_profile' => generer_url_public('echoppe_profile')));
		spip_log('ECHOPPE_INFO : infos de livraison pour paiement incomplète','echoppe');
	}
	
	if (!echoppe_valider_informations_facturation()){
		$erreurs['echoppe_non_configure'] = _T('echoppe:impossible_d_effectuer_le_paiement_echoppe_n_est_pas_completement_configure');
		spip_log('ECHOPPE_ERROR : configuration pour paiement incomplète','echoppe');
	}
	
	return $erreurs;
}

function formulaires_paiement_traiter_dist(){
	$messages = array();
	return $messages;
}

?>
