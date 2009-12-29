<?php
/**
 * Plugin catalogue pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_etape2_charger_dist(){

	$valeurs = array(
		'id_variante'=>'',
		'nom'=>'',
		'prenom' => '',
		'courriel' => '',
		'adresse' => '',
		'code_postal' => '',
		'ville' => '',
		'tel_bureau' => '',
		'tel_maison' => '',
		'tel_portable' => ''
	);
	/*
	*/
	return $valeurs;
}


function formulaires_etape2_verifier_dist(){
	
	$erreurs = array();
	/*
	
	// verifier la presence de tous les champs obligatoires
	foreach($champs_obligatoires as $obligatoire => $valeur){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = '*Ce champ est obligatoire';
	}
	
	include_spip('inc/validations');
	if (_request('courriel') AND !verif_email_apsulis(_request('courriel')))
		$erreurs['courriel'] = 'Cet email n\'est pas valide';	
	
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	*/
	return $erreurs;
}

function formulaires_etape2_traiter_dist(){
	$message_ok = "<p>Merci pour ces informations; vous allez maintenant saisir vos préférences.</p>";
	return array('message_ok'=>$message_ok);
}


?>