<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_modele_grille_foundation_dist($champs) {

	// Construire le haut de la grille
	$modele = '<'._request($champs[0]).'|large-up='._request($champs[1]).'|medium-up='._request($champs[2]).'|small-up='._request($champs[3]).'>';

	$modele .= '<fin_grille|>';

	return $modele;
}