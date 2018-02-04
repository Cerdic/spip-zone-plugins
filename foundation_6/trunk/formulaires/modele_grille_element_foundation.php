<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_modele_grille_element_foundation_dist($champs) {

	$modele = '<'._request($champs[0]).'|>';
	$modele .= _request($champs[1]);
	$modele .= '<fin_grille_element|>';

	return $modele;
}