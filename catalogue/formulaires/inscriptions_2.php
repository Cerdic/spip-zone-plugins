<?php
/**
 * Formulaire d'Inscription Ardesi
 * Apsulis (http://demo.apsulis.com) - XDjuj
 * Septembre 2009
 * 
 */

function formulaires_inscriptions_2_charger_dist($env){
	$valeurs = array(
		'formulaire1'=>unserialize($env),
		'presence'=>'',
		'atelier_17_matin'=>'',
		'atelier_17_apresmidi'=>''
	);
	
	return $valeurs;
}

function formulaires_inscriptions_2_verifier_dist(){
	include_spip('inc/validations');
	$erreurs = array();
	
	/* VERIF SUR LES CHAMPS OBLIGATOIRES */
	$champs_obligatoires = array(
		'presence'=>''
	);
	foreach($champs_obligatoires as $obligatoire => $valeur){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = '*Ce champ est obligatoire';
	}

	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	return $erreurs;
}

function formulaires_inscriptions_2_traiter_dist($env){
	$message_ok = "Etape 2 ok";
	return array('message_ok'=>$message_ok);
}

?>