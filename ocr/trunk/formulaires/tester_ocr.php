<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_tester_ocr_charger_dist(){
	//Valeurs prealablement saisie ou par defaut
	$valeur = array(
		'id_document' => '',
		'resultat' => '',
	);
	return $valeur;
}
function formulaires_tester_ocr_verifier_dist(){
	$erreurs = array();

	if((!_request('id_document'))||(_request('id_document') < 1)){
		$erreurs['id_document'] = _T('ocr:test_erreur_id_document');
	}
	return $erreurs;
}

function formulaires_tester_ocr_traiter_dist(){

	include_spip('inc/ocr_analyser');
	$resultat = ocr_analyser(_request('id_document'));

	if ($resultat['erreur']){
		return array(
			"editable" => true,
			"message_erreur" => _T('ocr:test_erreur_regarder_logs').' '.$resultat['erreur'],
		);
	}
	
	// envoi à la fonction charger
	if ($resultat['texte']) {
		set_request('resultat', $resultat['texte']);
	}
	
	// message
	return array(
		"editable" => true,
		"message_ok" => _T('ocr:test_message_resultat'),
	);
	
}
