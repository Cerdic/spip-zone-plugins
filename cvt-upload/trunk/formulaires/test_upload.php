<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_test_upload_charger(){
	$contexte = array(
		'tromperie' => '',
		'seul' => '',
		'plusieurs' => array(),
	);
	
	return $contexte;
}

function formulaires_test_upload_fichiers(){
	return array('seul', 'plusieurs');
}

function formulaires_test_upload_verifier(){
	$erreurs = array();
	
	if (_request('tromperie'))
		$erreurs['tromperie'] = 'Il ne fallait rien remplir.';
	
	return $erreurs;
}

function formulaires_test_upload_traiter(){
	$retours = array('message_ok' => 'Il ne se passe rien.');
	
	include_spip('inc/session');
	$hash = cvtupload_hash();
	$fichiers = session_get($hash.'_fichiers');
	var_dump($fichiers);
	
	return $retours;
}

?>
