<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_test_upload_charger(){
	$contexte = array(
		'tromperie' => '',
		'seul' => '',
		'plusieurs' => array(),
		'image' => '',
	);
	
	return $contexte;
}

function formulaires_test_upload_fichiers(){
	return array('seul', 'plusieurs', 'image');
}

function formulaires_test_upload_verifier(){
	$erreurs = array();
	
	if (_request('tromperie'))
		$erreurs['tromperie'] = 'Il ne fallait rien remplir.';

	$verifier = charger_fonction('verifier', 'inc', true);
	$options = array(
		'taille_max' => 250, // en Ko
		'largeur_max' => 800, // en px
		'hauteur_max' => 600, // en px
	);
	if( $erreur = $verifier($_FILES['image'], 'image', $options) ) {
		$erreurs['image'] = $erreur;
		unset($_FILES['image']);
	}
	
	return $erreurs;
}

function formulaires_test_upload_traiter(){
	$retours = array('message_ok' => 'Il ne se passe rien.');
	
	$fichiers = _request('_fichiers');
	var_dump($fichiers);
	
	return $retours;
}

?>
