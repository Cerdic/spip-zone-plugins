<?php
/**
 * Plugin catalogue pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_etape3_charger_dist(){
	$valeurs = array();
	return $valeurs;
}


function formulaires_etape3_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}

function formulaires_etape3_traiter_dist(){
	$message_ok = "<p>Merci pour ces informations; vous allez maintenant choisir votre mode de règlement.</p>";
	return array('message_ok'=>$message_ok);
}


?>