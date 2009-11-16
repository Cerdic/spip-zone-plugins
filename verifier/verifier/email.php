<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Verifie la validite d'une adresse de courriel.
 */
function verifier_email_dist($valeur, $options=array()){
	include_spip('inc/filtres');
	
	if (email_valide($valeur))
		return '';
	else
		return _T('verifier:erreur_email');
}

?>
