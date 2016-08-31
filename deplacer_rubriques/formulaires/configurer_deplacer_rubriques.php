<?php
function formulaires_configurer_deplacer_rubriques_charger_dist(){
		$valeurs = array('rubriques_a_deplacer'=>'','rubrique_cible'=>'','dry_run'=>'','confirmation'=>_request('confirmation'));
		return $valeurs;
}

function formulaires_configurer_deplacer_rubriques_verifier_dist(){
		$erreurs = array();
		foreach(array('rubriques_a_deplacer','rubrique_cible') as $obligatoire)
				if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	   
		if (_request('dry_run')=="oui")
				$erreurs['dry_run'] = 'oui';
 
		if (count($erreurs))
				$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		return $erreurs;
}

function formulaires_configurer_deplacer_rubriques_traiter_dist(){
		return array('message_ok'=>'');
}
