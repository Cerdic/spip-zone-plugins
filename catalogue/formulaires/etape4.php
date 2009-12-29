<?php
/**
 * Plugin catalogue pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_etape4_charger_dist(){
	$valeurs = array();
	return $valeurs;
}


function formulaires_etape4_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}

function formulaires_etape4_traiter_dist(){
	$message_ok = "<p>Merci pour ces informations; C'est terminé !</p>";
	return array('message_ok'=>$message_ok);
}


?>