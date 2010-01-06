<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 1/ Un SIREN comporte STRICTEMENT 9 caractères
 * 1b/ Un SIRET comporte strictement 14 caractères
 * 2/ Un siren/siret utilise une clef de controle "1-2"
 *    Un siren/siret est donc valide si la somme des chiffres paires
 *    + la somme du double de tous les chiffres impairs (16 = 1+6 = 7) est un multiple de 10
 */

function verifier_siren_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_siren');
	$ok = '';

	// Si pas 9 caractère, c'est déjà foiré !
	if(!preg_match('/^[0-9]{9}$/',$valeur)) return $erreur;
	
	// On vérifie la clef de controle "1-2"
	$somme = 0;
	$i = 0; // Les impaires
	while($i < 9){ $somme += $valeur[$i]; $i+=2; }
	$i = 1; // Les paires !
	while($i < 9){ if((2*$valeur[$i])>9) $somme += (2*$valeur[$i])-9; else $somme += 2*$valeur[$i]; $i+=2; }
	
	if($somme % 10) return $erreur;
	
	return $ok;
}
