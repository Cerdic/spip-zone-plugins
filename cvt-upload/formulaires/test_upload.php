<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_test_upload_charger(){
	$contexte = array(
		'tromperie' => '',
		'seul' => '',
		'plusieurs' => array()
	);
	
	$contexte['_champs_fichiers'] = array('seul', 'plusieurs');
	
	return $contexte;
}

function formulaires_test_upload_verifier(){
	$erreurs = array();
	
	if (_request('tromperie'))
		$erreurs['tromperie'] = 'Il ne fallait rien remplir.';
	
	return $erreurs;
}

function formulaires_test_upload_traiter(){
	$retours = array();
	
	return $retours;
}

?>
