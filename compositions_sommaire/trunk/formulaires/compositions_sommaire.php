<?php
/**
Docs :

Formulaires CVT par l'exemple : https://www.spip.net/fr_article3796.html

*/

// Charger
function formulaires_compositions_sommaire_charger_dist(){
		$valeurs = array('composition_sommaire'=>'');
		return $valeurs;
}
// Verifier
function formulaires_compositions_sommaire_verifier_dist(){
        $erreurs = array();
		
        return $erreurs;
}

// Traiter
function formulaires_compositions_sommaire_traiter_dist(){
        $compostion = ecrire_meta('compositions_sommaire',_request('composition_sommaire'));
        return array(
			'message_ok'=>'Votre composition a été pris en compte');
}
