<?php

/**
 * Fonctions spécifiques à une saisie
 *
 * @package SPIP\Saisies\input
**/


// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('saisies/calcul_fonctions');
/**
 * Vérifie que la valeur postée
 * correspond aux valeurs proposées lors de la config de valeur
 * @param string $valeur la valeur postée
 * @param array $description la description de la saisie
 * @return bool true si valeur ok, false sinon,
**/
function calcul_valeurs_acceptables($valeur, $description) {
	$expr = saisie_calcul_2_php($description['options']['calcul']);
	return $valeur == eval("return $expr;");
}

