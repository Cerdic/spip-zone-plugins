<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Un nombre est composé de chiffres
 */

function verifier_telephone_fr_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_telephone');
	$ok = '';
	// On accepte differentes notations, les points, les tirets, les espaces, les slashes
	$tel = ereg_replace("\.|/|-| ",'',$valeur);

	// On interdit les 000 etc. mais je pense qu'on peut faire plus malin
	// TODO finaliser les numéros à la con
	if($tel == '0000000000') return $erreur;
	
	if(!preg_match("/^0[0-9]{9}$/",$tel)) return $erreur;
	
	return $ok;
}
