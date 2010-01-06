<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Un nombre est composé de chiffres
 */

function verifier_numerique_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_numerique');
	$ok = '';
	if(!preg_match('/^[0-9]*$/',$valeur)) return $erreur;
	return $ok;
}
